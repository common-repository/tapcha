<?php

if (!class_exists('TapchaCF7')) {
    class TapchaCF7 {
        private $shortcode = 'tapcha*';

        function __construct() {
            add_action('wpcf7_init', array($this, 'add_custom_shortcode'));
            add_filter('wpcf7_validate_' . $this->shortcode, array($this, 'validate'), 20, 2);
        }

        function add_custom_shortcode() {
            wpcf7_add_form_tag($this->shortcode, array($this, 'render_shortcode'), array('name-attr' => true));
        }

        function render_shortcode($tag) {
            tapcha_load_js('tapcha-bootstrap-script', 'includes/js/bootstrap.min.js', array('jquery'));
            tapcha_load_js('tapcha-konva-script', 'includes/js/konva.min.js', array('jquery'));
            tapcha_load_js('tapcha-cf7-script', 'includes/js/tapcha-cf7.js', array('tapcha-bootstrap-script'));

            tapcha_load_css('tapcha-bootstrap-styles', 'includes/css/bootstrap.min.css');
            tapcha_load_css('tapcha-captcha-styles', 'includes/css/captcha.css');

            return $this->create_shortcode_html($tag->name);
        }

        function create_shortcode_html($tag_name) {
            $site_key = esc_attr(get_option("tapcha-site-key"));

            return '
                <div class="form-signin">
                    <div id="tapcha">
                        <div id="imageSection">
                            <img id="image" class="img-responsive" src="" alt="' . __('Tapcha instructions', 'tapcha') . '">
                        </div>
                        <div id="tapcha-canvas"></div>
                    </div>
    
                    <div id="tapcha-loading" class="spinner spinner-border"></div>
                    
                    <p id="tapcha-error-invalid-site-key" class="d-none">' . __('Invalid site key!', 'tapcha') . '</p>
                    <p id="tapcha-error" class="d-none">' . __('Error! Please contact the site administrator.', 'tapcha') . '</p>
    
    
                    <button type="button" class="btn btn-lg btn-secondary btn-block" id="tapcha-reload">
                        ' . __('Reload', 'tapcha') . '
                    </button>
                </div>
             
                <span class="wpcf7-form-control-wrap ' . $tag_name . '">
                    <input type="hidden" name="' . $tag_name . '" value="" size="40" class="" aria-required="true" aria-invalid="false">
                    <input type="hidden" name="tapcha-site-key" id="tapcha-site-key" value="' . $site_key . '">
                    <input type="hidden" name="tapcha-challenge-id" id="tapcha-challenge-id">
                    <input type="hidden" name="tapcha-challenge-answer" id="tapcha-challenge-answer">
                </span>
            ';
        }

        function validate($result, $tag) {
            $challenge_id = $this->get_challenge_id();
            $challenge_answer = $this->get_challenge_answer();

            if ($challenge_id == null || $challenge_answer == null) {
                $message = __('Failed Tapcha test! Invalid input.', 'tapcha');
                $result->invalidate($tag, $message);
                return $result;
            }

            $endpoint = "http://api.tapcha.co.uk/api/v1/response/" . $this->get_challenge_id();
            $request = array(
                'body' => array(
                    'challenge_answer' => $this->get_challenge_answer()
                )
            );

            $response = wp_safe_remote_post($endpoint, $request);
            $response_code = wp_remote_retrieve_response_code($response);

            if ($response_code != 200) {
                $message = __('Failed Tapcha test!', 'tapcha');
                if ($response == 429) {
                    $message = __('This challenge has already been consumed', 'tapcha');
                }
                $result->invalidate($tag, $message);
            }
            return $result;
        }

        function get_challenge_id() {
            // The challenge id must be at least a 36 character long UUID string
            // Example of expected value:
            // 95b8abe6-548b-3810-9757-1566a8c2b4b2

            $post_value = $_POST['tapcha-challenge-id'];

            if (!isset($post_value)) {
                return null;
            }

            $sanitized_value = filter_var($post_value, FILTER_SANITIZE_STRING);

            if ($sanitized_value == false || !is_string($sanitized_value) || strlen($sanitized_value) < 36 || strlen($sanitized_value) > 99) {
                return null;
            }

            return $sanitized_value;
        }

        function get_challenge_answer() {
            // Example of expected value:
            // [{"id":"066877f4-cfe8-3674-b4ce-f9d4bdf06001","xPosition":1,"yPosition":1},{"id":"02090142-f9a3-34d5-b409-ccdfc5e0a61e","xPosition":7,"yPosition":5},{"id":"1e1a7877-5bda-336c-acac-0be8fd32e147","xPosition":3,"yPosition":9},{"id":"4df4521b-6d7f-3600-aeb7-79346d86ca39","xPosition":4,"yPosition":1},{"id":"55807cd2-1f22-3d13-a598-9236058c4064","xPosition":2,"yPosition":1},{"id":"fa6b37fc-699b-3568-8a88-41c84fcac55f","xPosition":1,"yPosition":5},{"id":"5321e59b-b710-37d1-aaef-0650deb0b696","xPosition":1,"yPosition":9},{"id":"10100edd-5d2e-3db1-be9f-ffb93e8dded3","xPosition":2,"yPosition":1}]

            $post_value = $_POST['tapcha-challenge-answer'];

            if (!isset($post_value)) {
                return null;
            }

            $json = json_decode(stripslashes($post_value));

            if ($json == null || !is_array($json) || count($json) == 0) {
                return null;
            }

            $sanitized_values = array();

            foreach ($json as $item) {
                $id = null;
                if (key_exists('id', $item)) {
                    $id = filter_var($item->id, FILTER_SANITIZE_STRING);
                }

                $xPosition = null;
                if (key_exists('xPosition', $item)) {
                    if (is_float($item->xPosition)) {
                        $xPosition = filter_var($item->xPosition, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    } else if (is_int($item->xPosition)) {
                        $xPosition = filter_var($item->xPosition, FILTER_SANITIZE_NUMBER_INT);
                    }
                }

                $yPosition = null;
                if (key_exists('yPosition', $item)) {
                    if (is_float($item->yPosition)) {
                        $yPosition = filter_var($item->yPosition, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    } else if (is_int($item->yPosition)) {
                        $yPosition = filter_var($item->yPosition, FILTER_SANITIZE_NUMBER_INT);
                    }
                }

                $validId = $id != false && is_string($id) && strlen($id) >= 36 && strlen($id) < 100;

                $validXPosition = $xPosition != false && (is_numeric($xPosition) || is_float($xPosition)) && $xPosition >= 0 && $xPosition < 400;

                $validYPosition = $yPosition != false && (is_numeric($yPosition) || is_float($yPosition)) && $yPosition >= 0 && $yPosition < 400;

                if ($validId && $validXPosition && $validYPosition) {
                    array_push($sanitized_values, [
                        'id' => $id,
                        'xPosition' => $xPosition,
                        'yPosition' => $yPosition
                    ]);
                }
            }

            if (count($sanitized_values) > 0) {
                return json_encode($sanitized_values);
            }

            return null;
        }

        static function isCF7Activated() {
            return defined('WPCF7_VERSION') || defined('WPCF7_PLUGIN');
        }
    }
}

if (TapchaCF7::isCF7Activated()) {
    $tapcha_cf7 = new TapchaCF7();
}