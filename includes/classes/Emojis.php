<?php
 
class Emojis {
 
	static $emoji_list = "<img src='assets/images/emojis/1.png'>
						<img src='assets/images/emojis/2.png'>
						<img src='assets/images/emojis/3.png'>
						<img src='assets/images/emojis/4.png'>
						<img src='assets/images/emojis/5.png'>
						<img src='assets/images/emojis/6.png'>
						<img src='assets/images/emojis/7.png'>
						<img src='assets/images/emojis/8.png'>
						<img src='assets/images/emojis/9.png'>
						<img src='assets/images/emojis/10.png'>
						<img src='assets/images/emojis/11.png'>
						<img src='assets/images/emojis/12.png'>
						<img src='assets/images/emojis/13.png'>
						<img src='assets/images/emojis/14.png'>
						<img src='assets/images/emojis/15.png'>
						<img src='assets/images/emojis/16.png'>
						<img src='assets/images/emojis/17.png'>
						<img src='assets/images/emojis/18.png'>
						<img src='assets/images/emojis/19.png'>
						<img src='assets/images/emojis/20.png'>
						<img src='assets/images/emojis/21.png'>
						<img src='assets/images/emojis/22.png'>
						<img src='assets/images/emojis/23.png'>
						<img src='assets/images/emojis/24.png'>
						<img src='assets/images/emojis/25.png'>
						<img src='assets/images/emojis/26.png'>
						<img src='assets/images/emojis/27.png'>
						<img src='assets/images/emojis/28.png'>
						<img src='assets/images/emojis/29.png'>
						<img src='assets/images/emojis/30.png'>
						<img src='assets/images/emojis/31.png'>
						<img src='assets/images/emojis/32.png'>
						<img src='assets/images/emojis/33.png'>
						<img src='assets/images/emojis/34.png'>
						<img src='assets/images/emojis/35.png'>
						";
 
	static function createEmojis($body_array, $key, $value) {
 
		for($x = 1; $x <= 35; $x++) {
 
			if(strpos($value, ":s$x:") !== false) {
 
				$value = "<img src='assets/images/emojis/$x.png' width='30px' height='30px'>";
				$body_array[$key] = $value;
			}
		}
 
 
		return $body_array;
	}
	
}
?>