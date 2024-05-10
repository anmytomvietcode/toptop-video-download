jQuery(document).ready(function($) {
    $('#toptop-video-download-form').on('submit', function(e) {
        e.preventDefault();
        var video_url = $('#tiktok_video_url').val();

        $.ajax({
            url: toptop_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'toptop_video_download',
                tiktok_video_url: video_url,
                security: toptop_ajax.security // Passing the nonce
            },
            beforeSend: function() {
                // Hiển thị loader hoặc thông báo "đang tải..."
                $('#toptop-video-download-result').html('<p>Loading...</p>');
            },
            success: function(response) {
                // Xử lý dữ liệu trả về
                if (response.status === 'success') {
                    // Chuyển người dùng đến trang download với video_id và chuỗi ngẫu nhiên từ phản hồi
                    window.location.href = 'https://aitomatic.test/download-tiktok-media/' + response.random_string;
                } else {
                    // Hiển thị thông báo lỗi
                    $('#toptop-video-download-result').html('<p>' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                // Xử lý lỗi khi AJAX không thành công
                var errorMsg = xhr.status + ': ' + xhr.statusText;
                $('#toptop-video-download-result').html('<p>Error - ' + errorMsg + '</p>');
            }
        });
    });
});
