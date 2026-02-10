<?php
namespace AdvantagoLocationFinder\Services;

class YextApiClient
{
    private $apiKey;
    private $baseUrl = 'https://cdn.yextapis.com/v2';
    private $cacheTtl = 900; // default 15 minutes

    public function __construct($config)
    {
        $db_options = get_option('alf_settings', []);
        
        $this->apiKey = isset($db_options['yext_api_key']) && !empty($db_options['yext_api_key']) 
            ? (string)$db_options['yext_api_key'] 
            : (isset($config['yext_api_key']) ? (string)$config['yext_api_key'] : '');

        if (isset($db_options['cache_lifespan']) && (int)$db_options['cache_lifespan'] > 0) {
            $this->cacheTtl = (int)$db_options['cache_lifespan'];
        } elseif (isset($config['yext_cache_ttl']) && (int)$config['yext_cache_ttl'] > 0) {
            $this->cacheTtl = (int)$config['yext_cache_ttl'];
        }
    }

    /**
     * Fetch entities from Yext Content Delivery API.
     * @param array $params Additional query params
     * @return array
     */
    public function fetchEntities($params = array())
    {
        if (empty($this->apiKey)) {
            return array(
                'success' => false,
                'data' => null,
                'error' => 'Missing Yext API key in config.',
                'status' => null,
            );
        }

        $version = date('Ymd'); // Yext expects YYYYMMDD

        // Sanitize incoming params
        $safeParams = array();
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $key = sanitize_key($k);
                if ($key === '') { continue; }
                $safeParams[$key] = is_array($v) ? array_map('sanitize_text_field', $v) : sanitize_text_field($v);
            }
        }

        $query = array_merge($safeParams, array(
            'api_key' => $this->apiKey,
            'v' => $version,
        ));

        $url = $this->baseUrl . '/accounts/me/entities?' . http_build_query($query);

        // Build a stable cache key per unique request
        $cacheKey = 'alf_yext_entities_' . md5($this->apiKey . '|' . serialize($query));

        // Attempt to serve from cache if available
        if (function_exists('get_transient')) {
            $cached = get_transient($cacheKey);
            if ($cached !== false && is_array($cached) && isset($cached['success'])) {
                $cached['source'] = 'cache';
                return $cached;
            }
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'AdvantagoLocationFinder/1.0 (+https://wordpress.org)'
        ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

        $responseBody = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false) {
            return array(
                'success' => false,
                'data' => null,
                'error' => 'cURL error: ' . $curlErr,
                'status' => null,
            );
        }

        $json = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'success' => false,
                'data' => null,
                'error' => 'Invalid JSON from Yext: ' . json_last_error_msg(),
                'status' => $httpStatus,
            );
        }

        // Yext wraps responses with meta + response. Errors are in meta.errors
        if ($httpStatus < 200 || $httpStatus >= 300) {
            $err = isset($json['meta']['errors']) ? json_encode($json['meta']['errors']) : 'HTTP ' . $httpStatus;
            return array(
                'success' => false,
                'data' => null,
                'error' => 'HTTP error: ' . $err,
                'status' => $httpStatus,
            );
        }

        if (isset($json['meta']['errors']) && !empty($json['meta']['errors'])) {
            return array(
                'success' => false,
                'data' => null,
                'error' => 'API error: ' . json_encode($json['meta']['errors']),
                'status' => $httpStatus,
            );
        }

        $data = isset($json['response']) ? $json['response'] : $json; // fallback if structure differs

        $result = array(
            'success' => true,
            'data' => is_array($data) ? $data : array('raw' => $data),
            'error' => null,
            'status' => $httpStatus,
            'source' => 'api',
        );

        // Save to cache to minimize API calls
        if (function_exists('set_transient')) {
            set_transient($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }
}
