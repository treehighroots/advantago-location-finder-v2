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
        <p><small>Debug mode is on.</small></p>
    <?php endif; ?>
</div>
