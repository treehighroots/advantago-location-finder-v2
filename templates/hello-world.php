<?php
/**
 * Hello World Template
 *
 * Available variables:
 * @var string $message
 * @var array $config
 */
?>
<div class="alf-hello-world">
    <h1><?php echo esc_html($message); ?></h1>
    <?php if (isset($config['debug']) && $config['debug']) : ?>
        <p><small><?php _e('Debug mode is on.', 'advantago-location-finder'); ?></small></p>
    <?php endif; ?>
</div>
