<?php
/**
 * Plugin Name: Tapcha
 * Plugin URI: http://tapcha.co.uk
 * Description: A gesture based CAPTCHA scheme. Integrates with Contact Form 7
 * Version: 0.4.0
 * Author: Tapcha
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: tapcha
 * Domain Path: /languages/
 */
defined('ABSPATH') or die('You are not allowed to access this class directly');

define('TAPCHA_PLUGIN', __FILE__);
define('TAPCHA_PLUGIN_BASENAME', plugin_basename(TAPCHA_PLUGIN));
define('TAPCHA_PLUGIN_DIR', untrailingslashit(dirname(TAPCHA_PLUGIN)));

require_once TAPCHA_PLUGIN_DIR . '/includes/loaders.php';
require_once TAPCHA_PLUGIN_DIR . '/includes/contactform7.php';
require_once TAPCHA_PLUGIN_DIR . '/includes/shortcode.php';
require_once TAPCHA_PLUGIN_DIR . '/admin/admin.php';

if (!class_exists('TapchaPlugin')) {
    class TapchaPlugin {
        private $shortcode = 'tapcha';

        function __construct() {
            register_activation_hook(TAPCHA_PLUGIN, array($this, 'activate'));
            register_deactivation_hook(TAPCHA_PLUGIN, array($this, 'deactivate'));

            add_shortcode($this->shortcode, array($this, 'render_shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'load_shortcode_scripts'));
            add_action('plugins_loaded', array($this, 'tapcha_load_textdomain'));
        }

        function tapcha_load_textdomain() {
            load_plugin_textdomain('tapcha', false, basename(dirname(__FILE__)) . '/languages');
        }

        function activate() {
            flush_rewrite_rules();
        }

        function deactivate() {
            flush_rewrite_rules();
        }

        function render_shortcode() {
            $tapcha_shortcode = new TapchaShortcode();
            return $tapcha_shortcode->render();
        }

        function load_shortcode_scripts() {
            global $post;
            if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, $this->shortcode)) {
                tapcha_load_js('tapcha-bootstrap-script', 'includes/js/bootstrap.min.js', array('jquery'));
                tapcha_load_js('tapcha-konva-script', 'includes/js/konva.min.js', array('jquery'));
                tapcha_load_js('tapcha-shortcode-script', 'includes/js/tapcha-shortcode.js', array('tapcha-bootstrap-script'));

                tapcha_load_css('tapcha-bootstrap-styles', 'includes/css/bootstrap.min.css');
                tapcha_load_css('tapcha-captcha-styles', 'includes/css/captcha.css');
            }
        }
    }
}

$tapcha_plugin = new TapchaPlugin();