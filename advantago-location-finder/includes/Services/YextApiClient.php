<?php
namespace AdvantagoLocationFinder\Services;

class YextApiClient
{
    private $apiKey;
    private $baseUrl = 'https://cdn.yextapis.com/v2';

    public function __construct($config)
    {
        $this->apiKey = isset($config['yext_api_key']) ? (string)$config['yext_api_key'] : '';
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

        $query = array_merge($params, array(
            'api_key' => $this->apiKey,
            'v' => $version,
        ));

        $url = $this->baseUrl . '/accounts/me/entities?' . http_build_query($query);

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
            ),
        ));

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

        return array(
            'success' => true,
            'data' => is_array($data) ? $data : array('raw' => $data),
            'error' => null,
            'status' => $httpStatus,
        );
    }
}
