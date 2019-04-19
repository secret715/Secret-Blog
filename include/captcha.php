<?php
/*
<Secret Blog>
Copyright (C) 2012-2019 Secret <http://gdsecret.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, version 3.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Also add information on how to contact you by electronic and paper mail.

  If your software can interact with users remotely through a computer
network, you should also make sure that it provides a way for users to
get its source.  For example, if your program is a web application, its
interface could display a "Source" link that leads users to an archive
of the code.  There are many ways you could offer source, and different
solutions will be better for different programs; see section 13 for the
specific requirements.

  You should also get your employer (if you work as a programmer) or school,
if any, to sign a "copyright disclaimer" for the program, if necessary.
For more information on this, and how to apply and follow the GNU AGPL, see
<http://www.gnu.org/licenses/>.
*/

require_once('../config.php');
sb_captcha(6);

function genColor($r, $br = 0, $g = 0, $bg = 0, $b = 0, $bb = 0, $a = 0){
	global $image;
	
	if($g == 0 && $b == 0){
		$b = $g = $r;
	}
	
	$_r = mt_rand($br, $r);
	$_g = mt_rand($bg, $g);
	$_b = mt_rand($bb, $b);
	
	return imagecolorallocatealpha($image, $_r, $_g, $_b, $a);
}

$width =100;
$height = 40;
$font=array('captchafont.ttf','RiseStarHandRegular.otf');
$image = imagecreatetruecolor($width, $height);
$text = $_SESSION['captcha'];
$_font_size=$height * 0.2+10;
$_word_chunk=2;
$_length=strlen($text)/$_word_chunk;
$_width_chunk= ceil($width/($_length+1));
$bg = imagecolorallocate($image, 255, 255, 255);



imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $bg);

imagealphablending($image, true);

$pa=rand(3,5);
for($a = 1; $a <= $pa; $a++){
	$p = $a / $pa;
	imageline($image, 1, $height * $p, $width - 2, $height * $p, imagecolorallocate($image, 127, 186, 190));
	imageline($image, $width * $p, 1, $width * $p, $height - 2, imagecolorallocate($image, 127, 186, 190));
}


for($j=0;$j<$_length;$j++){
	imagettftext(
		$image,
		$_font_size,
		0,
		mt_rand($_width_chunk * $j, $_width_chunk * ($j+1)),
		mt_rand($_font_size, $height),
		genColor(mt_rand(0,150)),
		$font[array_rand($font)],
		substr($text, $j*$_word_chunk, $_word_chunk)
	);
}

for($i = 0; $i < rand(2,3); $i++){
	imagefilledarc($image, mt_rand($width * 0.055, $width * 0.8), mt_rand($height * 0.15, $height * 0.8), $height * 0.4, $height * 0.4, 0, 360, genColor(255, 180, 255, 110, 255, 90, 110), IMG_ARC_PIE);
}

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);