<?php
$id = $_POST['id'];
$name = $_POST['name'];
// Create a 300x100 image
$im = imagecreatetruecolor(140, 60);
$red = imagecolorallocate($im, 0xF6, 0xF3, 0xF3);
$black = imagecolorallocate($im, 0x00, 0x00, 0x00);

// Make the background red
imagefilledrectangle($im, 0, 0, 169, 59, $red);
// Path to our ttf font file
$font_file = 'NotoSansCJKtc-Bold.otf';
// Draw the text 'PHP Manual' using font size 13
imagefttext($im, 25, 0, 10, 40, $black, $font_file, $name);

// Output image to the browser
// header('Content-Type: image/png');
// imagepng($im);
// imagedestroy($im);
// exit;

header("Content-type: image/jpeg");

//創建目標圖像
$dst_im = @imagecreatefromjpeg("share.jpg");

//源圖像
$src_im = $im;

//拷貝源圖像左上角起始150px 150px
imagecopy( $dst_im, $src_im, 710, 317, 0, 0, 140, 60 );

//輸出拷貝後圖像
imagejpeg($dst_im,'share/'.$id.'.jpg');
imagedestroy($im);
imagedestroy($dst_im);










?>