<?php
include ('../config_json.php');
?>
<?php
@$pic_id = $_POST['id'];
@$relay_hour = $_POST['relay_hour'];
@$relay_minute = $_POST['relay_minute'];
$url = '';

if (date("Ymd") <= '20180306'){
	if (strlen($relay_hour) == 2 && strlen($relay_minute) == 2 && is_numeric($relay_hour) && is_numeric($relay_minute) && (intval($relay_hour) >= 0 || intval($relay_hour) <= 23) && (intval($relay_minute) >= 0 || intval($relay_minute) <= 59)){
		try {
			$imgs = array();
			$imgs[0] = '../source/'. substr($relay_hour, 0, 1) .'.png';
			$imgs[1] = '../source/'. substr($relay_hour, 1, 1) .'.png';
			$imgs[2] = '../source/'. substr($relay_minute, 0, 1) .'.png';
			$imgs[3] = '../source/'. substr($relay_minute, 1, 1) .'.png';
			$source_img = '../source/bg.jpg';

			$target_img = Imagecreatefromjpeg($source_img);
			$source= array();
			$pic =  $pic_id .'.jpg';
			foreach ($imgs as $k=>$v){
				$source[$k]['source'] = Imagecreatefrompng($v);
				$source[$k]['size'] = getimagesize($v);
			}
			imagecopy($target_img,$source[0]['source'],39,165,0,0,$source[0]['size'][0],$source[0]['size'][1]);
			imagecopy($target_img,$source[1]['source'],127,165,0,0,$source[1]['size'][0],$source[1]['size'][1]);
			imagecopy($target_img,$source[2]['source'],240,165,0,0,$source[2]['size'][0],$source[2]['size'][1]);
			imagecopy($target_img,$source[3]['source'],328,165,0,0,$source[3]['size'][0],$source[3]['size'][1]);

			Imagejpeg($target_img,'..'. $pic_folder . $pic);

			$url = 'https://'. $_SERVER['HTTP_HOST'] . $pic_folder . $pic;
		} catch (Exception $e) {
		}
	}
}
$json_result = array(
	"url" => $url
);

echo json_encode($json_result);
?>
