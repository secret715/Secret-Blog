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

if(!isset($_GET['id'])or trim($_GET['id'])==''){
	header('Location: index.php');
	exit;
}

if(isset($_POST['title']) && isset($_POST['content']) && trim($_POST['title'])!='' && trim($_POST['content'])!='' && isset($_GET[$_SESSION['Blog_Auth']]) && trim($_POST['keyword']['0'])!='' && trim($_POST['title'])!='' && trim($_POST['content'])!=''){

	$keyword=implode(',',$_POST['keyword']);
	
	$SQL->query("UPDATE `post` SET `title`='%s', `content`='%s', `public`='%d', `keyword`='%s', `update_time`=now() WHERE `id`='%d'",array(htmlspecialchars($_POST['title']),$_POST['content'],$_POST['public'],htmlspecialchars(trim($keyword,',')),abs($_GET['id'])));
	
	unset($_SESSION['autosave'][intval($_GET['id'])]);
	$_ok=true;
}


$page=sb_get_result("SELECT * FROM `post` WHERE `id`='%d' AND `type`=1",array(abs($_GET['id'])));

if(isset($_SESSION['autosave'][$page['row']['id']])){
	$data=json_decode($_SESSION['autosave'][$page['row']['id']]);
	$page['row']['content']=$data[1];
	unset($_SESSION['autosave'][$page['row']['id']]);
	$_autosave=true;
}

$keyword=explode(',',$page['row']['keyword']);

if($page['num_rows']<1){
	header('Location: page.php');
	exit;
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'編輯頁面',true);
$view->addScript('../include/js/ckeditor/ckeditor.js');
$view->addScript('../include/js/fileupload/jquery.ui.widget.js');
$view->addScript('../include/js/fileupload/jquery.iframe-transport.js');
$view->addScript('../include/js/fileupload/jquery.fileupload.js');
$view->addScript('../include/js/upload.js');
$view->addScript('../include/js/autosave.js');
?>
<script>
$(function(){
	var editor = CKEDITOR.replace('content');
	sb_autosave(<?php echo $page['row']['id']?>);
	sb_filemanager(<?php echo $page['row']['id']?>,'<?php echo $_SESSION['Blog_Auth']; ?>');
	sb_uploader(<?php echo $page['row']['id']?>,10000*1000,'<?php echo $_SESSION['Blog_Auth']; ?>');
	window.onbeforeunload = function(event) {
	  event.returnValue = true;
	}
	$('form').submit(function(){
		window.onbeforeunload = null;
	});
	$('.alert.alert-success').delay(1000).fadeOut(500);
});
</script>
<?php if(isset($_ok)){ ?>
<div class="alert alert-success">
	修改成功！
</div>
<?php }elseif(isset($autosave)){ ?>
<div class="alert alert-success">
	已自動復原您編輯的內容！
</div>
<?php } ?>
<form action="editpage.php?id=<?php echo $page['row']['id'].'&'.$_SESSION['Blog_Auth']; ?>" method="POST">
	<div class="row">
		<div class="col-md-9">
			<fieldset>
				<legend>編輯頁面<?php if($page['row']['public']=='1'){ ?><a style="font-size:60%;margin-left:10px;" href="<?php echo sb_post_url($page['row']['id'],'../page',$blog['url_rewrite']); ?>">檢視</a><?php } ?></legend>
				<input class="form-control" name="title" type="text" placeholder="標題" value="<?php echo $page['row']['title']; ?>" required="required">
				<textarea class="form-control ckeditor" name="content"><?php echo stripslashes($page['row']['content']); ?></textarea>
			</fieldset>
		</div>
		<div class="col-md-3">
			<fieldset>
				<legend>設定</legend>
				<div class="form-group">
					<label>附加檔案：</label>
					<div class="btn-group">
						<a id="filemanager_btn" class="btn btn-sm btn-primary" href="#filemanager" data-toggle="modal"><span class="glyphicon glyphicon-th-large"></span> 管理</a>
						<a id="upload_btn" class="btn btn-sm btn-success" href="#upload" data-toggle="modal"><span class="glyphicon glyphicon-upload"></span> 上傳</a>
					</div>
				</div>
				<div class="form-group">
					<label for="public">狀態：</label>
					<select class="form-control" name="public">
					<?php
					$_public=sb_post_public_array();
					for($i=0;$i<count($_public);$i++){
					?>
						<option value="<?php echo $i; ?>"<?php if($page['row']['public']==$i){ echo ' selected="selected"';} ?>><?php echo $_public[$i]; ?></option>
					<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="keyword">關鍵字：</label><br>
					<?php for($i=0;$i<10;$i++){ ?>
					<input class="form-control" name="keyword[<?php echo $i; ?>]" type="text" value="<?php if(isset($keyword[$i])){echo $keyword[$i];} ?>" maxlength="20" style="display:inline-block;width:48%;" <?php if($i==0){echo 'required="required"';} ?>>
					<?php } ?>
				</div>
			</fieldset>
			<div class="form-group">
				<input type="submit" class="btn btn-success btn-lg" value="儲存">
			</div>
		</div>
	</div>
</form>
<div id="filemanager" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 class="modal-title">管理附加檔案</h3>
			</div>
			<div class="modal-body"><p class="text-info text-center" style="font-size:150%;">載入中...</p></div>
			<div class="modal-footer">
				<a class="btn btn-default" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span> 關閉</a>
			</div>
		</div>
	</div>
</div>
<div id="upload" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 class="modal-title">上傳檔案</h3>
			</div>
			<div id="uploader" class="modal-body">
				<div id="drop" class="well">
					<p>將檔案拖曳到這裡</p>
					<input id="fileupload" name="files" type="file" multiple>
				</div>
				<div id="uploadinfo">
					<div id="progress" class="progress" style="margin:0 auto;width: 80%;">
						<div class="progress-bar progress-bar-success" style="width: 0%;"></div>
					</div>
					<table class="table item">
						<tr>
							<th></th>
							<th>檔案</th>
							<th>URL</th>
							<th>進度</th>
						</tr>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-default" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove"></span> 關閉</a>
			</div>
		</div>
	</div>
</div>
<?php $view->render(); ?>