<?php

namespace TheLion\Integrations\Divi;

if (!defined('LETSBOX_VERSION')) {
    return;
}

class LetsBox extends \ET_Builder_Module
{
    public $slug = 'wpcp_letsbox';
    public $vb_support = 'on';
    public $use_raw_content = true;

    protected $module_credits = [
        'module_uri' => 'https://wpcloudplugins.com',
        'author' => 'WP Cloud Plugins',
        'author_uri' => 'https://wpcloudplugins.com',
    ];

    public function init()
    {
        $this->name = 'Box Module';

        $this->settings_modal_toggles = [
            'general' => [
                'toggles' => [
                    'main_content' => 'Module',
                ],
            ],
        ];

        $this->advanced_fields = [
            'background' => false,
            'borders' => false,
            'box_shadow' => false,
            'button' => false,
            'filters' => false,
            'fonts' => false,
            'margin_padding' => false,
            'text' => false,
            'link_options' => false,
            'height' => false,
            'scroll_effects' => false,
            'animation' => false,
            'transform' => false,
        ];
    }

    public function get_fields()
    {
        return [
            'shortcode' => [
                'label' => esc_html__('Module Configuration', 'wpcloudplugins'),
                'type' => 'wpcp_shortcode_field',
                'option_category' => 'configuration',
                'description' => esc_html__('Configure this module using the Module Builder or select an existing module.', 'wpcloudplugins'),
                'default' => '',
                'ajax_url' => LETSBOX_ADMIN_URL,
                'plugin_slug' => 'letsbox',
                'toggle_slug' => 'main_content',
            ],
        ];
    }

    public function render($attrs, $content = null, $render_slug = '')
    {
        $shortcode = html_entity_decode($this->props['shortcode']);
        if (empty($shortcode)) {
            return '<div style="text-align:center;">⚠ '.esc_html__('This WP Cloud Plugin module is not yet configured.', 'wpcloudplugins').'</div>';
        }

        \ob_start();

        echo do_shortcode($shortcode);

        $output = \ob_get_clean();

        if (empty($output)) {
            return '';
        }

        return $output;
    }
}

new LetsBox();
