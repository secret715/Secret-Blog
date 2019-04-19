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

set_include_path('../include/');
$includepath = true;

require_once('../Connections/SQL.php');
require_once('../config.php');
require_once('view.php');

if(!isset($_SESSION['Blog_Username']) or $_SESSION['Blog_UserGroup']<5){
	header("Location: ../index.php");
	exit;
}

if(isset($_POST['title']) && isset($_POST['content']) && trim($_POST['title'])!='' && trim($_POST['content'])!='' && isset($_GET[$_SESSION['Blog_Auth']]) && trim($_POST['title'])!='' && trim($_POST['content'])!=''){
	$keyword=implode(',',$_POST['keyword']);
	
	$SQL->query("INSERT INTO `post` (`title`, `content`, `type`, `public`, `comment`, `class`, `keyword`, `mktime`, `update_time`, `author`) VALUES ('%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s', now(), '%d')",array(htmlspecialchars($_POST['title']),$_POST['content'],1,$_POST['public'],0,0,htmlspecialchars(trim($keyword,',')),date('Y-m-d H:i:s'),$_SESSION['Blog_Id']));
	header('Location: page.php?newpage');
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'新增頁面',true);
$view->addScript('../include/js/ckeditor/ckeditor.js');
?>
<?php if(isset($_ok)){ ?>
<div class="alert alert-success">
	新增成功！
</div>
<?php } ?>
<form action="newpage.php?<?php echo $_SESSION['Blog_Auth']; ?>" method="POST">
	<div class="row">
		<div class="col-md-9">
			<fieldset>
				<legend>新增頁面</legend>
				<input class="form-control" name="title" type="text" placeholder="標題" required="required">
				<textarea class="form-control ckeditor" name="content"></textarea>
			</fieldset>
		</div>
		<div class="col-md-3">
			<fieldset>
				<legend>設定</legend>
				<div class="form-group">
					<label>附加檔案：</label>
					<p class="text-danger" style="font-size:120%;">儲存後方可使用附加檔案</p>
				</div>
				<div class="form-group">
					<label for="public">狀態：</label>
					<select class="form-control" name="public">
					<?php
					$_public=sb_post_public_array();
					for($i=0;$i<count($_public);$i++){
					?>
						<option value="<?php echo $i; ?>"><?php echo $_public[$i]; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="keyword">關鍵字：</label><br>
					<?php for($i=0;$i<10;$i++){ ?>
						<input class="form-control" name="keyword[<?php echo $i; ?>]" type="text" maxlength="20" style="display:inline-block;width:48%;" <?php if($i==0){echo 'required="required"';} ?>>
					<?php } ?>
				</div>
			</fieldset>
			<div class="form-group">
				<input type="submit" class="btn btn-success btn-lg" value="儲存">
			</div>
		</div>
	</div>
</form>
<?php $view->render(); ?>