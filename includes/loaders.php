<?php

function tapcha_load_php($fileName) {
    require_once TAPCHA_PLUGIN_DIR . '/' . $fileName;
}

function tapcha_load_css($handle, $fileName, $dependencies = array()) {
    wp_enqueue_style($handle, plugins_url($fileName, TAPCHA_PLUGIN_BASENAME), $dependencies);
}

function tapcha_load_js($handle, $fileName, $dependencies = array()) {
    wp_enqueue_script($handle, plugins_url($fileName, TAPCHA_PLUGIN_BASENAME), $dependencies);
}