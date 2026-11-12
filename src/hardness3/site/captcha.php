<?php
session_start();
// Output the image
header('Content-type: image/png');
// Create a 100*30 image
$im = imagecreate(50, 30);

// White background and blue text
$bg = imagecolorallocate($im, 255, 255, 255);
$textcolor = imagecolorallocate($im, 0, 0, 0);

$key = 'hard123';
$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($_SESSION['generatedCode']), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
imagestring($im, 5, 0, 0, $decrypted, $textcolor);

imagepng($im);
imagedestroy($im);