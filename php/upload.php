<?php
if(is_file($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php')){
    require_once($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php');
}
$file_name = date("Ymdhis") . rand(1, 10000)  . '.jpg';

header("Content-type: text/html; charset=utf-8"); 

if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status(0, 'Error! Wrong HTTP method!');
}

if(array_key_exists('file',$_FILES) && $_FILES['file']['error'] == 0 ){
	$file = $_FILES['file'];
    $ext = get_extension($file['name']);
	/* 后缀名判断不安全 */
	$allowed_ext = array('png', 'jpg', 'jpeg', 'gif');
	if (! in_array($ext, $allowed_ext)) {
		exit_status('error', 'Only Image can upload!');
	}
	if ($file['size'] > 10 * 1024 * 1024) {
		exit_status('error', 'Max file size is 5MB!');
	}
	
	$form_data = $file['tmp_name'];
	$dumpdata = file_get_contents($form_data);
	
	$img = new SaeImage();
    $img->setData($dumpdata );
    $img->resize(600); // 等比缩放到600宽
	
	$new_data = $img->exec('jpg'); // 执行处理并返回处理后的二进制数据
	//转换失败
	if ($new_data === false) {
        exit_status('error', 'p1:' . $img->errmsg());
	}
	$size = $img->getImageAttr();
	
	//覆盖加入3张图片
	$logo_ten_year_img = file_get_contents('../res/images/logo_ten_year110.png');
	$logo_zfb_img = file_get_contents('../res/images/logo_zfb100.png');
	$text_bg_img = file_get_contents('../res/images/text_back350.png');
	
	//清空$img数据
	$img->clean();

	//设定要用于合成的三张图片（如果重叠，排在后面的图片会盖住排在前面的图片）
	$img->setData( array(
		array( $new_data, 0, 0, 1, SAE_TOP_LEFT ),//左上角
		array( $logo_ten_year_img, -10, 10, 1, SAE_BOTTOM_RIGHT ),//右下角
		array( $logo_zfb_img, -10, -10, 1, SAE_TOP_RIGHT ), //右上角
		array( $text_bg_img, 0, -47, 1, SAE_TOP_LEFT ) //左上角+偏移
	) );

	//执行合成
	$img->composite($size[0], $size[1]);
 
	//输出图片
	$new_data = $img->exec('jpg');
	if ($new_data === false) {
        exit_status('error', 'p2:' . $img->errmsg());
	}
	
	$s = new SaeStorage();
    $result = $s->write( 'rank' , $file_name, $new_data );
    if(!$result) {
        exit_status('fail', $result);
    }
	$url = $s->getUrl('rank', $file_name);
	
	exit_status('ok', $url);
}
else {
    exit_status('fail', 'no file accept!');
}
// Helper functions
function exit_status($status, $data){
	echo json_encode(array('status'=>$status, 'data'=>$data));
	exit;
}

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}
?>