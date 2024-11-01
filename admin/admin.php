<?php

if (!class_exists('TapchaAdmin')) {
    class TapchaAdmin {
        private $settings_page;
        private $settings_page_slug = 'tapcha-options';

        function __construct() {
            add_action('admin_menu', array($this, 'build_admin_page'));
            add_action('admin_init', array($this, 'create_options'));
            add_filter('plugin_action_links_' . TAPCHA_PLUGIN_BASENAME, array($this, 'update_plugin_links'));
        }

        function build_admin_page() {
            $this->settings_page = add_options_page(__('Tapcha', 'tapcha'), __('Tapcha', 'tapcha'), 'manage_options', $this->settings_page_slug, array($this, 'render_admin_page'));
            add_action('admin_enqueue_scripts', array($this, 'add_scripts'));
        }

        function render_admin_page() {
            tapcha_load_php('admin/includes/admin-page.php');
        }

        function add_scripts($hook) {
            if ($hook != $this->settings_page) {
                return;
            }
            tapcha_load_js('tapcha-admin-script', 'admin/includes/js/admin.js');
            tapcha_load_css('tapcha-admin-styles', 'admin/includes/css/admin.css');
        }

        function create_options() {
            $option_group = 'tapcha_options_group';

            add_option($option_group);

            register_setting($option_group, 'tapcha-site-key');
            register_setting($option_group, 'tapcha-admin-key');

            $settings_section = 'tapcha-settings-section';
            $settings_page = 'tapcha-settings-page';

            add_settings_section($settings_section, __('Credentials', 'tapcha'), array($this, 'render_credentials_section'), $settings_page);
            add_settings_field('tapcha-site-key', __('Site Key', 'tapcha'), array($this, 'render_site_key'), $settings_page, $settings_section);
            add_settings_field('tapcha-admin-key', __('Admin Key', 'tapcha'), array($this, 'render_admin_key'), $settings_page, $settings_section);
        }

        function render_credentials_section() {
            tapcha_load_php('admin/includes/section-credentials.php');
        }

        function render_site_key() {
            tapcha_load_php('admin/includes/field-site-key.php');
        }

        function render_admin_key() {
            tapcha_load_php('admin/includes/field-admin-key.php');
        }

        function update_plugin_links($links) {
            $admin_url = admin_url('options-general.php?page=' . $this->settings_page_slug);
            $settings_text = __('Settings', 'tapcha');

            $settings_link = '<a href="' . $admin_url . '">' . $settings_text . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }
    }
}

$tapcha_plugin = new TapchaAdmin();