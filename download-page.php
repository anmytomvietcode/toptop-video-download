<?php
// Prevent direct access to the file
if (!defined('WPINC')) {
    die;
}

// Kiểm tra xem thông tin video đã được lưu trong transient chưa

$random_string = get_query_var('download-tiktok-media'); // Get the random string from the URL
$video_data = get_transient('video_data_' . $random_string); // Use the random string to retrieve the transient

if (!$video_data) {
    wp_die('Không có thông tin video để hiển thị hoặc thông tin đã hết hạn.');
}

// Nhúng video TikTok
$embed_url = "https://www.tiktok.com/embed/v3/{$video_data['video_id']}";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Download TikTok Video</title>
    <link rel="stylesheet" href="<?php echo esc_url(plugin_dir_url(__FILE__) . 'style.css'); ?>">
</head>
<body>

<div class="toptop-video-download-container">
    <iframe src="<?php echo esc_url($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
    
    <div class="toptop-download-buttons">
        <?php if (isset($video_data['video1'])): ?>
            <a href="<?php echo esc_url($video_data['video1']); ?>" class="toptop-download-button" download target="_blank">Download 1</a>
        <?php endif; ?>
        <?php if (isset($video_data['video2'])): ?>
            <a href="<?php echo esc_url($video_data['video2']); ?>" class="toptop-download-button" download target="_blank">Download 2</a>
        <?php endif; ?>
        <?php if (isset($video_data['video_hd'])): ?>
            <a href="<?php echo esc_url($video_data['video_hd']); ?>" class="toptop-download-button" download target="_blank">Download HD</a>
        <?php endif; ?>
        <?php if (isset($video_data['music'])): ?>
            <a href="<?php echo esc_url($video_data['music']); ?>" class="toptop-download-button" download target="_blank">Download MP3</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>