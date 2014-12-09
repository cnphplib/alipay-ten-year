<?php
header("content-type:image/jpeg");

$text = array();
$text[] = isset($_GET['t1']) ? trim($_GET['t1']) : '';
$text[] = isset($_GET['t2']) ? trim($_GET['t2']) : '';
$text[] = isset($_GET['t3']) ? trim($_GET['t3']) : '';

if (empty($text[0]) && empty($text[1]) && empty($text[2])) {
	$text[0] = '钱包比男人靠得住';
	$text[1] = '每天早上刷余额宝';
	$text[2] = '你懂的！';
//	$text[0] = ' ';
//	$text[1] = ' ';
//	$text[2] = ' ';
}

//模版图片
/*
$temp_img_path = isset($_GET['t']) ? trim($_GET['t']) : ''; #一个数字代号，找到相应的路片路径，用代码空间图片的即可
if (empty($temp_img_path)) {
	//没有没有传递模版图片，随机一张
	$temp_img_path = '../res/images/templete600.jpg';
}

$temp_img = imagecreatefromjpeg($temp_img_path);
$temp_width = imagesx($temp_img);
$temp_height = imagesy($temp_img);
*/
//上传图片
$up_img_url = isset($_GET['u']) ? trim($_GET['u']) : '';#正常情况下，是从storage中的路径
if (empty($up_img_url)) {
	#没有上传，随机一张
	$up_img_url = '../res/images/empty.jpg';
}
$rst_img = imagecreatefromjpeg($up_img_url);
$up_width = imagesx($rst_img);
$up_height = imagesy($rst_img);
#放缩图片
//$up_img_resized = resizeImageByHeight($up_img, $temp_width);

//合并图片
//$rst_img = mergeImg($temp_img, $up_img_resized);


//文字
$location_x = array(15, 15, 15);
$location_y = array(133, 175, 217);
//y - 303 + temp_height

$font_src = 'hylxtj.ttf';
$font_size = 27;
$font_color = array(255, 255, 255);
$sy_color = imagecolorallocate($rst_img, $font_color[0], $font_color[1], $font_color[2]);

for ($i = 0; $i < count($text); $i ++ ) {
	//$ttf_im = imagettfbbox($font_size, 0, $font_src, $text[$i]);
	imagettftext($rst_img, $font_size, 5, $location_x[$i], $location_y[$i], $sy_color, $font_src, $text[$i]);
}

#输出到浏览器
imagejpeg($rst_img);
imagedestroy($rst_img);

function mergeImg($img1, $img2) {
	$pic_width = imagesx($img1);
	$pic_height = imagesy($img1) + imagesy($img2);
	
	$dst_img = imagecreatetruecolor($pic_width, $pic_height);
	imagealphablending($dst_img, true);
	imagecopyresampled($dst_img, $img1, 0, 0, 0, 0, $pic_width, imagesy($img1), $pic_width, imagesy($img1));
	imagecopyresampled($dst_img, $img2, 0, imagesy($img1), 0, 0, $pic_width, imagesy($img2), $pic_width, imagesy($img2));
	
	imagedestroy($img1);
	imagedestroy($img2);
	
	return $dst_img;
}


function resizeImageByHeight($im, $maxwidth) {
    $pic_width = imagesx($im);
    $pic_height = imagesy($im);
	
	#按照宽度压缩
	$widthratio = $maxwidth / $pic_width;
	
	$newheight = $widthratio * $pic_height;
	if(function_exists("imagecopyresampled")) {
        $newim = imagecreatetruecolor($maxwidth, $newheight);
		imagealphablending($newim, true);
		imagecopyresampled($newim,$im,0,0,0,0,$maxwidth, $newheight, $pic_width, $pic_height);
    }
    else {
        $newim = imagecreate($maxwidth, $newheight);
		imagealphablending($newim, true);
        imagecopyresized($newim,$im,0,0,0,0,$maxwidth, $newheight, $pic_width, $pic_height);
    }
	imagedestroy($im);
    return $newim;
}
?>

