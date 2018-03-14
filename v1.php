<?php
// 註冊或查詢你的 API Keys: https://www.google.com/recaptcha/admin
$siteKey = '6LcWk0wUAAAAADSbEA_9o0ize7PeRJP9aH3LdBB6';

// 所有支援的語系: https://developers.google.com/recaptcha/docs/language
$lang = 'zh-TW';
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <meta charset="UTF-8">
    <title>Ajax jQuery | reCAPTCHA 範例</title>
    
    <!-- Google jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=zh-TW&onload=onloadCallback&render=explicit" async defer></script>
    <script type="text/javascript">
    /*
     * recaptcha API 參考 https://developers.google.com/recaptcha/docs/display
     */
    var verifyCallback = function(response) {
        // 如果 JavaScript 驗證成功
        if (response) {
            $.post('captcha.php', { 'g-recaptcha-response': response }, function(data, status) {
                // 如果 PHP 驗證成功
                if (status == 'success') $('#success').text('驗證成功');
                else $('#success').text('驗證失敗');
            });
        }
    };

    var onloadCallback = function() {
        grecaptcha.render(
            'my-widget', {                              // widget 驗證碼視窗在 id="my-widget" 顯示
                'sitekey' : '<?php echo $siteKey; ?>',  // API Key
                'callback' : verifyCallback,            // 要呼叫的回調函式
                'theme' : 'dark'                        // 主題
            }
        );
    };
    </script>

    <!-- Google reCAPTCHA icon -->
    <link rel="shortcut icon" href="//www.gstatic.com/recaptcha/admin/favicon.ico" type="image/x-icon"/>
</head>
<body>
    
    <!-- 顯示驗證成功與否 -->
    <h1 id="success"></h1>

    <!-- reCAPTCHA 小工具出現的位置 -->
    <!-- POSTs back to the page's URL upon submit with a g-recaptcha-response POST parameter. -->
    <div id="my-widget"></div>

</body>
</html>