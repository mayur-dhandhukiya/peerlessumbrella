<?php

/**
 * This file should be used to render each module instance.
 * You have access to two variables in this file:.
 *
 * $module An instance of your module class.
 * $settings The module's settings.
 */
$shortcode = $settings->raw_shortcode;
if (empty($shortcode)) {
    return '⚠ '.esc_html__('This WP Cloud Plugin module is not yet configured.', 'wpcloudplugins');
}

\ob_start();

echo do_shortcode($shortcode);

$content = \ob_get_clean();

if (empty($content)) {
    echo '';
}

echo $content;
