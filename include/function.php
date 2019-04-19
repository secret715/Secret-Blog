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

function sb_ver(){
	return '2.1';
}
function sb_login($_username,$_password){
	global $SQL;
	if (isset($_username)&&isset($_password)) {
		$_username=strtolower($_username);
		$login = $SQL->query("SELECT `id`, `username`, `password`, `level` FROM `member` WHERE (`username` = '%s' OR `email` = '%s') AND `password` = '%s'",array(
			$_username,
			$_username,
			sb_password($_password,$_username)
		));
		
		
		//[相容] 舊版密碼----開始
		if ($login->num_rows <1) {
			$login = $SQL->query("SELECT * FROM `member` WHERE (`username` = '%s' OR `email` = '%s') AND `password` = '%s'",array(
			$_username,
			$_username,
			md5(sha1($_password))
			));
			if($login->num_rows > 0){
				$SQL->query("UPDATE `member` SET `password` = '%s' WHERE `username` = '%s'",array(sb_password($_password,$_username),$_username));
			}
		}//[相容] 舊版密碼----結束
		
		
		if ($login->num_rows > 0) {
			$info = $login->fetch_assoc();
			
			$SQL->query("UPDATE `member` SET `last_login` = now() WHERE `username` = '%s'",array($info['username']));
			
			$_SESSION['Blog_Username'] = $_username;
			$_SESSION['Blog_Id'] = $info['id'];
			$_SESSION['Blog_UserGroup'] = $info['level'];
			$_SESSION['Blog_Auth'] = substr(sb_keygen(),0,5);	      
			setcookie("login", time(), time()+86400);
			return 1;
		}
		else {
			return -1;
		}
	}
}
function sb_loginout(){
	$_SESSION['Blog_Username'] = NULL;
	$_SESSION['Blog_Id'] = NULL;
	$_SESSION['Blog_UserGroup'] = NULL;
	$_SESSION['Blog_Auth'] = NULL;
	unset($_SESSION['Blog_Username']);
	unset($_SESSION['Blog_Id']);
	unset($_SESSION['Blog_UserGroup']);
	unset($_SESSION['Blog_Auth']);
	setcookie("login", "", time()-60);
	return 1;
}

function sb_get_result($_query,$_value=array()){
	global $SQL;
	$_result['query'] = $SQL->query($_query,$_value);
	$_result['row'] = $_result['query']->fetch_assoc();
	$_result['num_rows'] = $_result['query']->num_rows;
	if($_result['num_rows']>0){
		return $_result;
	}else{
		return -1;
	}
}
function sb_post_public_array(){
	return array('私密','公開','草稿','限已登入會員');
}
function sb_post_public($_public){
	$_array=sb_post_public_array();
	$_return='<span class="label label-';
	switch ($_public){
		case 0:
			$_return.='default">'.$_array[0];
			break;
		case 1:
			$_return.='success">'.$_array[1];
			break;
		case 2:
			$_return.='warning">'.$_array[2];
			break;
		default:
			$_return.='info">'.$_array[$_public];
	}
	$_return.= '</span>';
	return $_return;
}

function sb_page_list(){
	global $SQL;
	global $blog;
	$page_list=sb_get_result("SELECT * FROM `post` WHERE `type`=1 AND `public`=1 ORDER BY `id` ASC");
	$_return='';
	if($page_list['num_rows']>0){
		do{
			$_return.='<li><a href="'.sb_post_url($page_list['row']['id'],'page',$blog['url_rewrite']).'">'.$page_list['row']['title'].'</a></li>';
		}while($page_list['row'] = $page_list['query']->fetch_assoc());
	}
	return $_return;
}

function sb_member_level_array(){
	return array(0=>'作者',5=>'編輯',9=>'管理員');
}

function sb_member_level($_level){
	$_array=sb_member_level_array();
	return $_array[$_level];
}

function sb_namefilter($_value){
	$_array=array('/' => '' , '\\' => '' , '*' => '' ,':' => '' , '?' => '' , '<'  => '' , '>' => '','│' => '');
	return strtr($_value,$_array);
}

function sb_password($_value,$_salt){
	$salt=substr(sha1(strrev($_value).$_salt),0,24);
	return hash('sha512',$salt.$_value);
}

function sb_get_headurl(){
	$_prefix='http';
	if(isset($_SERVER['HTTPS'])){
		if($_SERVER['HTTPS'] == 'on'){
			$_prefix='https';
		}
	}elseif(isset($_SERVER['HTTP_CF_VISITOR'])){
		$_cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
		if (isset($_cf_visitor->scheme) && $_cf_visitor->scheme == 'https') {
		  $_prefix='https';
		}
	}
	$url="$_prefix://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}";
	$po= strripos($url,'/');
	return substr($url,0,$po).'/';
}

function sb_keygen($_value=''){
	return str_shuffle(str_replace('=','',base64_encode(time()).md5(mt_rand().$_value.uniqid())));
}

function sb_post_gist($_content,$_split,$_html=true,$_newline=true){
	$_content=stripslashes($_content);
	if($_html==false){
		$_content=htmlspecialchars_decode($_content);
	}
	if($_newline==false){
		$_content=str_replace("\r",'',str_replace("\n",'',$_content));
	}
	$_return=mb_substr($_content,0,mb_strpos($_content,$_split,0,'UTF-8'),'UTF-8');
	return $_return;
}
function sb_post_description($_content,$_length){
	$_content=str_replace("\r",'',str_replace("\n",'',strip_tags(stripslashes($_content))));
	$_return=mb_substr($_content,0,$_length,'UTF-8');
	return $_return;
}
function sb_post_url_array(){
	return array('.php?id=%s','-%s.html');
}
function sb_post_url($_id,$_prefix,$_type=0){
	$_array=sb_post_url_array();
	if(isset($_array[$_type])){
		$_model=$_prefix.$_array[$_type];
	}else{
		$_model=$_prefix.$_array[0];
	}
	return sb_get_headurl().sprintf($_model,$_id);
}
function sb_page_pagination($_href,$_now_page,$_data_num,$_page_limit,$_href_parameters=''){
	$_return='';
	$_now_page=abs($_now_page);
	$page_num= ceil($_data_num / $_page_limit);
	if($page_num>=7){
		$_return.='<ul class="pagination">';
		$_array=array(1,2,3,$_now_page-2,$_now_page-1,$_now_page,$_now_page+1,$_now_page+2,$page_num-2,$page_num-1,$page_num);
		$_array=array_unique($_array);
		$_last_value=0;
		foreach ($_array as $value){
			if($value>0&&$value<=$page_num){
				if($_last_value+1!=$value){
					$_return.='<li><span>...</span></li>';
				}
				if($_now_page==$value){
					$_return.='<li class="active"><span>'.$value.'</span></li>';
				}else{
					$_return.='<li><a href="'.$_href.'?page='.$value.$_href_parameters.'">'.$value.'</a></li>';
				}
				$_last_value=$value;
			}
		}
		$_return.='</ul>';
	}elseif($page_num>1){
		$_return.='<ul class="pagination">';
		for($i=1;$i<=$page_num;$i++){
			if($_now_page!=$i){
				$_return.='<li><a href="'.$_href.'?page='.$i.$_href_parameters.'">'.$i.'</a></li>';
			}else{
				$_return.='<li class="active"><span>'.$i.'</span></li>';
			}
		}
		$_return.='</ul>';
	}
	return $_return;
}
function sb_deletedir($dir) {
    if ($handle = opendir($dir)) {
        while(false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir($dir."/".$item)) {
                    deletedir($dir."/".$item);
                } else {
                    unlink($dir."/".$item);
                }
            }
        }    
        closedir($handle);
        rmdir($dir);
      }
}
function sb_size($_size){
	if($_size<0){
		$_size=abs($_size);
		$_sign='-';
	}else{
		$_sign='';
	}
	if($_size<1200){
		return $_sign.round($_size,2).' Byte';
	}elseif($_size/1000<1200){
		return $_sign.round($_size/1000,2) .' KB';
	}elseif($_size/1000/1000<1200){
		return $_sign.round($_size/1000/1000,2) .' MB';
	}elseif($_size/1000/1000/1000<1200){
		return $_sign.round($_size/1000/1000/1000,2) .' GB';
	}elseif($_size/1000/1000/1000/1000<1200){
		return $_sign.round($_size/1000/1000/1000/1000,2) .' TB';
	}else{
		return $_sign.round($_size/1000/1000/1000/1000/1000,2) .' PB';
	}
}

function sb_img_compress($_image,$_image_type,$_new_image_file,$_new_height,$_new_image_quality=75){
	switch ($_image_type){
		case 'jpg':
		case 'jpeg':
			$_origin_img = imagecreatefromjpeg($_image);
			//判斷轉向
			if(function_exists('exif_read_data')){
			$_exif = exif_read_data($_image);
				if(!empty($_exif['Orientation'])) {
					switch($_exif['Orientation']) {
						case 8:
							$_origin_img = imagerotate($_origin_img,90,0);
							break;
						case 3:
							$_origin_img = imagerotate($_origin_img,180,0);
							break;
						case 6:
							$_origin_img = imagerotate($_origin_img,-90,0);
							break;
					}
				}
			}
			break;
		case 'png':
			$_origin_img = imagecreatefrompng($_image);
			break;
		case 'gif':
			$_origin_img = imagecreatefromgif($_image);
			break;
		default:
			return false;
	}
	
	
	$_origin_width = imagesx($_origin_img);
	$_origin_height = imagesy($_origin_img);

	if($_origin_height>$_new_height){
		$_new_width=intval($_origin_width / $_origin_height * $_new_height);

		//建立新的圖
		$_new_image = imagecreatetruecolor($_new_width, $_new_height);

		//將原始照片縮小並複製到新的圖中
		//imagecopyresized($_new_image, $_origin_img, 0, 0, 0, 0, $_new_width, $_new_height, $_origin_width, $_origin_height);
		imagecopyresampled($_new_image, $_origin_img, 0, 0, 0, 0, $_new_width, $_new_height, $_origin_width, $_origin_height);
	
		imagejpeg($_new_image, $_new_image_file, $_new_image_quality);//輸出JPG圖片
		return true;
	
	}else{
		imagejpeg($_origin_img, $_new_image_file, $_new_image_quality);//輸出JPG圖片
		return true;
	}
}

function sb_captcha($_length=6,$_type=2){
	// type 為驗證碼字串的形式(是否包含英文數字...)
	$_text[0]=array(0,1,2,3,4,5,6,7,8,9);//純數字
	$_text[1]=array('a','b','c','d','e','f','g','h','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');//純英文
	$_text[2]=array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z',2,3,4,5,6,7,8,9);//英文+數字(去除 L 、 1 、 O 跟 0)
	
	if($_length<=0||!isset($_text[$_type]))return false;
	
	
	$_array = $_text[$_type];
	
	$_captcha = '';
	for($_i = 0; $_i < $_length; $_i++){
		$_captcha .= $_array[mt_rand(0, count($_array) - 1)];
	}
	
	$_captcha = strtoupper($_captcha);
    $_SESSION['captcha'] = $_captcha;
}



function sb_log($_value,$_file='error.php'){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$_ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$_ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
		$_ip=$_SERVER['REMOTE_ADDR'];
	}
	
	$_log=vsprintf('[%s][%s] %s',array($_ip,date('Y-m-d H:i:s'),$_value));
	file_put_contents($_file,$_log."\n",FILE_APPEND);
}