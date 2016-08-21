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

if((isset($_POST['username']))&&(isset($_POST['password']))&&($_POST['username']!='')&&($_POST['password']!='')){

	if(strtoupper($_POST['captcha']) != strtoupper($_SESSION['captcha'])){
		header("Location: index.php?captcha");
		exit;
	}
	unset($_SESSION['captcha']);
	if(sb_login($_POST['username'],$_POST['password'])==1){
		header('Location: post.php');
		exit;
	}else{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		$_error=sprintf('%s 在 %s 嘗試登入 %s 失敗',$ip,date('Y-m-d H:i:s'),$_POST['username']);
		file_put_contents('error.php',$_error."\n",FILE_APPEND);
		$_GET['no']=true;
	}
}elseif(isset($_GET['out'])){
	sb_loginout();
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'登入',true);
?>
<?php if(isset($_GET['captcha'])){ ?>
	<div class="alert alert-danger">請檢查驗證碼！</div>
<?php } ?>
<script type="text/javascript">
$(function(){
	$('.captcha').on('click', function(e){
		e.preventDefault();
		$(this).attr('src', '../include/captcha.php?_=' + (new Date).getTime());
	});
});
</script>
<div class="page-header">
	<h2>登入</h2>
</div>
<form class="form-horizontal form-sm" action="index.php" method="POST">
	<div class="form-group">
		<label class="col-sm-3 control-label" for="username">帳號：</label>
		<div class="col-sm-6">
			<input class="form-control" name="username" type="text" placeholder="帳號" required="required">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="password">密碼：</label>
		<div class="col-sm-6">
			<input class="form-control" name="password" type="password" placeholder="密碼" required="required">
		</div>
		<div class="col-sm-3"></div>
	</div>
	<div class="form-group">
		<label class="col-xs-3 control-label" for="captcha">驗證碼：</label>
		<div class="col-xs-4">
			<input class="form-control" name="captcha" type="text" placeholder="驗證碼" maxlength="10" required="required">
		</div>
		<div class="col-xs-5">
			<img class="captcha" src="../include/captcha.php" title="按圖更換驗證碼">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<input type="submit" class="btn btn-primary btn-lg" value="登入">
		</div>
		<div class="col-sm-3"></div>
	</div>
</form>
<?php $view->render(); ?>