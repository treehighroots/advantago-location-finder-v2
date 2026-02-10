<?php
/** @var array $config */
/** @var array $result */
?>
<div class="alf-yext-entities">
    <?php if (empty($result) || empty($result['success'])): ?>
        <div class="alf-yext-error">
            <strong>Error:</strong>
            <?php echo isset($result['error']) ? esc_html($result['error']) : 'Unknown error.'; ?>
        </div>
        <?php if (!empty($config['debug']) && !empty($result['status'])): ?>
            <div class="alf-yext-debug">
                HTTP Status: <?php echo (int)$result['status']; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        $data = isset($result['data']) ? $result['data'] : array();
        $entities = isset($data['entities']) ? $data['entities'] : array();
        ?>
        <div class="alf-yext-summary">
            Retrieved <?php echo isset($data['count']) ? (int)$data['count'] : count($entities); ?> entities.
        </div>
        <?php if (!empty($entities)): ?>
            <ul class="alf-yext-entity-list">
                <?php foreach ($entities as $entity): ?>
                    <li>
                        <?php
                        $name = isset($entity['name']) ? $entity['name'] : (isset($entity['id']) ? ('ID ' . $entity['id']) : 'Unnamed');
                        echo esc_html($name);
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <pre class="alf-yext-json"><?php echo esc_html(wp_json_encode($data, JSON_PRETTY_PRINT)); ?></pre>
        <?php endif; ?>
    <?php endif; ?>
</div>
