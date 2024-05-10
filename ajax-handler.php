<?php
// Prevent direct access to the file
if (!defined('WPINC')) {
    die;
}

// Kiểm tra xem yêu cầu đến từ AJAX không
if (isset($_POST['action']) && $_POST['action'] === 'toptop_video_download') {
    // Khởi tạo mảng phản hồi
    $response = array('status' => 'error', 'message' => 'Có lỗi xảy ra');

    // Lấy URL video TikTok từ đầu vào
    $video_url = isset($_POST['tiktok_video_url']) ? sanitize_text_field($_POST['tiktok_video_url']) : '';

    // Kiểm tra định dạng URL video TikTok
    // Ví dụ: https://www.tiktok.com/@username/video/1234567890
    if (preg_match('/(https?:\/\/www\.tiktok\.com\/@[^\/]+\/video\/\d+)/', $video_url, $matches)) {
        $video_id = $matches[1];

        // Lấy API servers từ database hoặc cung cấp mặc định
        $api_servers_option = get_option('toptop_video_download_api_servers', '');
        $api_servers = $api_servers_option ? explode(',', $api_servers_option) : array(
            'https://tikdldtapi-iota.vercel.app/download/json?url=',
            'https://tikdldtapi.vercel.app/download/json?url=',
        );

        // Chọn một API server ngẫu nhiên
        $api_url = trim($api_servers[array_rand($api_servers)]) . urlencode($video_url);

        // Gửi yêu cầu đến API và lấy kết quả
        $api_response = wp_remote_get($api_url);
        
        if (is_wp_error($api_response)) {
            $response['message'] = 'Không thể kết nối với API: ' . $api_response->get_error_message();
        } else {
            $api_data = json_decode(wp_remote_retrieve_body($api_response), true);
            if ($api_data && isset($api_data['status']) && $api_data['status'] === 'success') {
                // Lưu các link video và music vào transient thay vì sử dụng session
                set_transient('video_data_' . $video_id, $api_data['result'], 60 * 60);  // Lưu trong 1 giờ
            
                // Tạo chuỗi ngẫu nhiên
                $random_string = bin2hex(openssl_random_pseudo_bytes(5)); // Tạo chuỗi ngẫu nhiên 10 ký tự hex
            
                $response = array('status' => 'success', 'video_id' => $video_id, 'random_string' => $random_string);
            } else {
                $response['message'] = 'Không thể lấy dữ liệu từ API';
            }
        }
    } else {
        $response['message'] = 'URL video TikTok không đúng định dạng';
    }

    // Trả về dữ liệu JSON cho AJAX
    echo json_encode($response);
    wp_die(); // Chấm dứt yêu cầu AJAX trong WordPress
} else {
    echo 'No direct access allowed!';
    exit;
}
?>