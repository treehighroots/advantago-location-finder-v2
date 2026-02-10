<?php
/** @var array $config */
/** @var array $result */
?>
<div class="alf-yext-entities">
    <?php if (empty($result) || empty($result['success'])): ?>
        <div class="alf-yext-error">
            <strong><?php _e('Error:', 'advantago-location-finder'); ?></strong>
            <?php echo isset($result['error']) ? esc_html($result['error']) : __('Unknown error.', 'advantago-location-finder'); ?>
        </div>
        <?php if (!empty($config['debug']) && !empty($result['status'])): ?>
            <div class="alf-yext-debug">
                <?php _e('HTTP Status:', 'advantago-location-finder'); ?> <?php echo (int)$result['status']; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        $data = isset($result['data']) ? $result['data'] : array();
        $entities = isset($data['entities']) ? $data['entities'] : array();
        ?>
        <div class="alf-yext-summary">
            <?php printf(__('Retrieved %d entities. (pls watch synchronized and not synchronized)/hidden entities on yext)', 'advantago-location-finder'), isset($data['count']) ? (int)$data['count'] : count($entities)); ?>
        </div>
        <div class="alf-yext-source">
            <?php
            $source = isset($result['source']) ? $result['source'] : 'unknown';
            printf(__('Data source: %s', 'advantago-location-finder'), esc_html($source));
            ?>
        </div>
        <?php if (!empty($entities)): ?>
            <ul class="alf-yext-entity-list">
                <?php foreach ($entities as $entity): ?>
                    <li>
                        <?php
                        $name = isset($entity['name']) ? $entity['name'] : (isset($entity['id']) ? ('ID ' . $entity['id']) : __('Unnamed', 'advantago-location-finder'));
                        echo esc_html($name);
                        echo isset($entity['c_bewertungswidget']) ? $entity['c_bewertungswidget'] : '';
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <pre class="alf-yext-json"><?php echo esc_html(wp_json_encode($data, JSON_PRETTY_PRINT)); ?></pre>
        <?php endif; ?>
    <?php endif; ?>
</div>
