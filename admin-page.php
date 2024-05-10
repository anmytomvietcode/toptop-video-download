<?php
// Kiểm tra quyền truy cập
if (!current_user_can('manage_options')) {
    wp_die(__('Bạn không có quyền truy cập trang này.'));
}

// Lưu cài đặt khi form được submit
if (isset($_POST['submit']) && check_admin_referer('toptop_video_download_settings_update', 'toptop_video_download_settings_nonce')) {
    // Lưu API servers vào database
    update_option('toptop_video_download_api_servers', sanitize_text_field($_POST['api_servers']));
}

// Lấy danh sách API servers từ database
$api_servers = get_option('toptop_video_download_api_servers', '');

?>

<div class="wrap">
    <h2>Toptop Video Download Settings</h2>
    <form method="post" action="">
        <?php
        // Tạo nonce field cho security check
        wp_nonce_field('toptop_video_download_settings_update', 'toptop_video_download_settings_nonce');
        ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="api_servers">API Servers:</label>
                </th>
                <td>
                    <input type="text" id="api_servers" name="api_servers" value="<?php echo esc_attr($api_servers); ?>" class="regular-text" />
                    <p class="description">
                        Nhập danh sách API servers, mỗi server cách nhau bằng dấu phẩy.
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>

<?php