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

if(!isset($_SESSION['Blog_Username']) or $_SESSION['Blog_UserGroup']<9){
	header("Location: ../index.php");
	exit;
}

if(isset($_POST['username']) && isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	$_POST['username']=strtolower($_POST['username']);
	$auth=sb_get_result("SELECT COUNT(*) FROM `member` WHERE `username`='%s'",array($_POST['username']));
	if(implode('',$auth['row'])==0){
		$SQL->query("INSERT INTO `member` (`username`, `password`, `nickname`, `email`, `level`, `joined`, `last_login`) VALUES ('%s', '%s', '%s', '%s', '%d', now(), now())",array($_POST['username'],sb_password($_POST['password'],$_POST['username']),$_POST['nickname'],$_POST['email'],$_POST['level']));
		$_save=true;
	}else{
		$_name=true;
	}
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'新增帳號',true);

if(isset($_save)){
?>
<div class="alert alert-success">
	新增成功！
</div>
<?php }elseif(isset($_name)){ ?>
<div class="alert alert-danger">
	此帳號已被使用！
</div>
<?php } ?>
<div class="page-header">
	<h2>新增帳號</h2>
</div>
<form class="form-horizontal form-sm" action="newaccount.php" method="POST">
	<div class="form-group">
		<label class="col-sm-3 control-label">帳號：</label>
		<div class="col-sm-6">
			<input class="form-control" name="username" type="text">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="nickname">暱稱：</label>
		<div class="col-sm-6">
			<input class="form-control" name="nickname" type="text" required="required">
		</div>
		<div class="col-sm-3 help-block">
			顯示於文章作者
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="password">密碼：</label>
		<div class="col-sm-6">
			<input class="form-control" name="password" type="password" maxlength="40" required="required">
		</div>
		<div class="col-sm-3 help-block">
			建議長度10以上
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="email">電子信箱：</label>
		<div class="col-sm-6">	
			<input class="form-control" name="email" type="email" maxlength="255" required="required">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="level">權限：</label>
		<div class="col-sm-6">
			<select class="form-control" name="level">
			<?php foreach(sb_member_level_array() as $key=>$value){ ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php } ?>
			</select>
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<input type="submit" class="btn btn-primary" value="新增">
		</div>
		<div class="col-sm-3"></div>
	</div>
</form>
<?php $view->render(); ?>