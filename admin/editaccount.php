<?php
/*
<Secret Blog>
Copyright (C) 2012-2016 太陽部落格站長 Secret <http://gdsecret.com>

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

if(!isset($_SESSION['Blog_Username'])){
	header("Location: ../index.php");
	exit;
}

if(isset($_GET['id'])&&$_SESSION['Blog_UserGroup']>=9){
	$_BlogENV['id']=abs($_GET['id']);
}else{
	$_BlogENV['id']=$_SESSION['Blog_Id'];
}

$member=sb_get_result("SELECT * FROM `member` WHERE `id`='%d'",array($_BlogENV['id']));
if($member['num_rows']<1){
	header("Location: index.php");
}

if(isset($_GET['id']) && isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	
	if($_POST['password']==''){
		$pass=$member['row']['password'];
	}else{
		$pass=sb_password($_POST['password'],$member['row']['username']);
	}
	
	if(isset($_POST['level'])&&$member['row']['id']!=$_SESSION['Blog_Id']){
		$_level=abs($_POST['level']);
	}else{
		$_level=$member['row']['level'];
	}
	
	$SQL->query("UPDATE `member` SET `nickname`='%s', `password`='%s', `email`='%s', `level`='%d' WHERE `id`='%d'",array($_POST['nickname'],$pass,$_POST['email'],$_level,$_BlogENV['id']));
	$_save=true;
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'編輯帳號',true);

if(isset($_save)){
?>
<div class="alert alert-success">
	編輯成功！
</div>
<?php } ?>
<div class="page-header">
	<h2>編輯帳號</h2>
</div>
<form class="form-horizontal form-sm" action="editaccount.php?id=<?php echo $_BlogENV['id']; ?>" method="POST">
	<div class="form-group">
		<label class="col-sm-3 control-label">帳號：</label>
		<div class="col-sm-6">
			<input class="form-control" name="username" value="<?php echo $member['row']['username']; ?>" disabled="disabled" type="text">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="nickname">暱稱：</label>
		<div class="col-sm-6">
			<input class="form-control" name="nickname" type="text" value="<?php echo $member['row']['nickname']; ?>" required="required">
		</div>
		<div class="col-sm-3 help-block">
			顯示於文章作者
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="password">密碼：</label>
		<div class="col-sm-6">
			<input class="form-control" name="password" type="password" maxlength="40">
		</div>
		<div class="col-sm-3 help-block">
			留空則不修改
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="email">電子信箱：</label>
		<div class="col-sm-6">	
			<input class="form-control" name="email" type="email" maxlength="255" value="<?php echo $member['row']['email']; ?>" required="required">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="level">權限：</label>
		<div class="col-sm-6">
			<?php if($member['row']['id']!=$_SESSION['Blog_Id']){ ?>
			<select class="form-control" name="level">
			<?php foreach(sb_member_level_array() as $key=>$value){ ?>
				<option value="<?php echo $key; ?>" <?php if($member['row']['level']==$key){ ?>selected="selected"<?php } ?>><?php echo $value; ?></option>
			<?php } ?>
			</select>
			<?php }else{ ?>
				<p class="form-control-static"><?php echo sb_member_level($member['row']['level']); ?></p>
			<?php } ?>
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<input type="submit" class="btn btn-primary" value="修改">
			<a class="btn btn-default" href="account.php">取消</a>
		</div>
		<div class="col-sm-3"></div>
	</div>
</form>
<?php $view->render(); ?>