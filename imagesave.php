<?php
			$source_img = '../image/share.jpg';
            $target_img = Imagecreatefromjpeg($source_img);
            imagecopy($target_img,$source[0]['source'],39,165,0,0,$source[0]['size'][0],$source[0]['size'][1]);

?>