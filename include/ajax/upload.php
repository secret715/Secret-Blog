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

set_include_path('../../include/');
$includepath = true;
require_once('../../Connections/SQL.php');
require_once('../../config.php');

if(!isset($_SESSION['Blog_Username'])){
	header("Location: ../../index.php");
	exit;
}
if(isset($_GET['p']) && abs($_GET['p'])!='' && isset($_GET[$_SESSION['Blog_Auth']])){
	$_auth=sb_get_result("SELECT `type` FROM `post` WHERE `id`='%d'",array(abs($_GET['p'])));
	if(($_auth['num_rows']<1) or ($_auth['row']['type']==1 && $_SESSION['Blog_UserGroup']<5)){
		exit;
	}
	$_dir = '../../upload/'.abs($_GET['p']);
	$_headurl = str_replace('include/ajax/','',sb_get_headurl());
	if(isset($_GET['list'])){
		if(!is_dir("$_dir/")) {
			echo '<p class="text-danger text-center" style="font-size:150%;">沒有檔案</p>';
			exit;
		}
		$_count_file = count(glob($_dir . "/*.*"));
		if($_count_file<1){
			echo '<p class="text-danger text-center" style="font-size:150%;">沒有檔案</p>';
			exit;
		}
		$handle = @opendir($_dir) or die("無法打開" . $_dir);
		?>
		<table class="table">
			<tr>
				<th>檔案</th>
				<th>大小</th>
				<th>上傳日期</th>
				<th></th>
			</tr>
		<?php
			while($file = readdir($handle)){
				if($file != "." && $file != ".."){ 
				$myfile = $_dir.'/'.$file;
				$_file_url = $_headurl.'upload/'.abs($_GET['p']).'/'.$file;
				$_file_ext = pathinfo($myfile, PATHINFO_EXTENSION);
		?>
			<tr>
				<td><?php if(in_array($_file_ext,array('png','gif','jpg','jpeg'))){ ?>
					<img src="<?php echo $_file_url; ?>" style="max-height:50px;"><?php } ?>
					<a href="<?php echo $_file_url; ?>" target="_blank"><?php echo $file ?></a>
				</td>
				<td><?php echo sb_size(filesize($myfile)); ?></td>
				<td><small><?php echo date('Y-m-d H:i',filemtime($myfile)); ?></small></td>
				<td><span class="file_remove btn btn-danger btn-sm" data-url="del=<?php echo $file; ?>&p=<?php echo abs($_GET['p']).'&'.$_SESSION['Blog_Auth']; ?>">刪除</span></td>
			</tr>
		<?php
			}
		}
		?>
		</table>
	<?php
		clearstatcache();
		closedir($handle);
		exit;
	}elseif(isset($_GET['del']) && sb_namefilter(trim($_GET['del']))!=''){
		unlink($_dir.'/'.sb_namefilter($_GET['del']));
		echo 1;
		exit;
	}

	$_Input=@$_FILES['files'];

	if(isset($_Input) && $_Input['error'] == 0 && $_GET['p'] && abs($_GET['p'])!=''){
		$_max_upload_size = 10;//單位為MB
		$_extend = pathinfo($_Input['name'], PATHINFO_EXTENSION);//文件副檔名
		$_allow_ext=array('png','gif','jpg','zip','pdf','docx','pptx','xlsx','doc','ppt','xls','odt','odp','ods');
		$_file_name=sb_namefilter(mb_substr(rtrim(strtr($_Input['name']," ","_"),'.'.$_extend),0,40,'utf-8')).'.'.$_extend;//檔案名稱
		if(file_exists($_dir.'/'.$_file_name)){
			for($i=0; $i<100; $i++){
				$_auth_file_name=rtrim($_file_name,'.'.$_extend).'_('.$i.').'.$_extend;
				if(file_exists($_dir.'/'.$_auth_file_name)){
					continue;
				}else{
					$_file_name=$_auth_file_name;
					break;
				}
			}
		}
		if(!in_array($_extend,$_allow_ext)){
			echo '{"status":"error","msg":"不允許此格式"}';
			exit;
		}
		if($_max_upload_size*1000*1000 < $_Input['size']){
			echo '{"status":"error","msg":"超過檔案大小限制(最高上限：'.$_max_upload_size.' MB)"}';
			exit;
		}
		if(!is_dir("$_dir/")) {  //檢查資料夾是否存在
			if(!mkdir("$_dir/")){  //不存在的話就創建資料夾
				echo '{"status":"error","msg":"新增資料夾失敗"}';
				exit;
			}
		}
		move_uploaded_file($_FILES['files']['tmp_name'],$_dir.'/'.$_file_name);
		
		if($blog['post']['compress']==true&&in_array($_extend,array('png','gif','jpg'))){
			$_new_file_name=rtrim($_file_name,'.'.$_extend).'.jpg';
			sb_img_compress($_dir.'/'.$_file_name,$_extend,$_dir.'/'.$_new_file_name,960,90);
			if($_new_file_name!=$_file_name){
				@unlink($_dir.'/'.$_file_name);
			}
			$_file_name=$_new_file_name;
		}
		
		echo '{"status":"success","url":"'.$_headurl.'upload/'.abs($_GET['p']).'/'.$_file_name.'"}';
	}else{
		echo '{"status":"error","msg":"上傳失敗，錯誤代碼：'. $_Input['error'].'"}';
	}

}
exit;