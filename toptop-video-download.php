<?php
/**
 * Plugin Name: Toptop Video Download
 * Description: Cho phép tải video TikTok.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access to the file
if (!defined('WPINC')) {
    die;
}

// Khởi tạo plugin
function toptop_video_download_init() {
    add_shortcode('toptop_video_download_form', 'toptop_video_download_form_shortcode');
    add_action('wp_enqueue_scripts', 'toptop_video_download_enqueue_scripts');
    add_action('wp_ajax_toptop_video_download', 'toptop_video_download_ajax_handler');
    add_action('wp_ajax_nopriv_toptop_video_download', 'toptop_video_download_ajax_handler');
    add_action('admin_menu', 'toptop_video_download_admin_menu');
}

add_action('plugins_loaded', 'toptop_video_download_init');

// Thêm shortcode
function toptop_video_download_form_shortcode() {
    ob_start();
    ?>
    <form id="toptop-video-download-form" method="post" action="">
        <input type="text" name="tiktok_video_url" id="tiktok_video_url" required>
        <input type="submit" value="Submit">
    </form>
    <div id="toptop-video-download-result"></div>
    <?php
    return ob_get_clean();
}

// Enqueue scripts
function toptop_video_download_enqueue_scripts() {
    wp_enqueue_style('toptop-video-download-css', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('toptop-video-download-js', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery'), '1.0', true);
    wp_localize_script('toptop-video-download-js', 'toptop_ajax', array('ajaxurl' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('toptop_video_download_nonce')));
}

// Xử lý AJAX
function toptop_video_download_ajax_handler() {
    // Check for nonce security
    $nonce = $_POST['security'] ?? '';
    if (!wp_verify_nonce($nonce, 'toptop_video_download_nonce')) {
        wp_send_json_error('Nonce verification failed.');
    }

    // Include the AJAX handler
    include 'ajax-handler.php';
}

// Thêm trang settings vào admin menu
function toptop_video_download_admin_menu() {
    add_options_page('Toptop Video Download Settings', 'Toptop Video Download', 'manage_options', 'toptop-video-download-settings', 'toptop_video_download_settings_page');
}

// Trang settings
function toptop_video_download_settings_page() {
    include 'admin-page.php';
}

// Hàm kích hoạt plugin
function toptop_video_download_activate() {
    toptop_video_download_rewrite_endpoint(); // Thêm endpoint
    flush_rewrite_rules(); // Flush rewrite rules
}

// Hàm vô hiệu hóa plugin
function toptop_video_download_deactivate() {
    flush_rewrite_rules(); // Flush rewrite rules
}

register_activation_hook(__FILE__, 'toptop_video_download_activate');
register_deactivation_hook(__FILE__, 'toptop_video_download_deactivate');

// Thêm rewrite endpoint
function toptop_video_download_rewrite_endpoint() {
    add_rewrite_endpoint('download-tiktok-media', EP_ROOT);
}

add_action('init', 'toptop_video_download_rewrite_endpoint');

// Thêm query vars
function toptop_video_download_query_vars($vars) {
    $vars[] = 'download-tiktok-media';
    return $vars;
}

add_filter('query_vars', 'toptop_video_download_query_vars');

// Template redirect
function toptop_video_download_template_redirect() {
    global $wp_query;
    if (isset($wp_query->query_vars['download-tiktok-media'])) {
        include plugin_dir_path(__FILE__) . 'download-page.php';
        exit;
    }
}

add_action('template_redirect', 'toptop_video_download_template_redirect');
?>