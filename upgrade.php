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

error_reporting(0);
if(isset($_GET['step'])&&$_GET['step']>0&&$_GET['step']<=4){
	$_step=abs($_GET['step']);
}else{
	$_step=0;
}

$error = false;

function check($val){
    global $error;
    if($val){
    	echo '<span style="color:green;">√</span>';
	}
	else {
		$error = true;
		echo '<span style="color:red;">Χ</span>';
	}
}

function check_php_version($version){
	check(phpversion() >= $version);
}

function check_extension($ext){
	check(extension_loaded($ext));
}

if($_step==2){
	if($_POST['radio']=='rename'){
		rename('upgrade.php','upgrade.txt');
		if(file_exists('install.php')){
			rename('install.php','install.txt');
		}
	}else{
		unlink('upgrade.php');
		if(file_exists('install.php')){
			unlink('install.php');
		}
	}
	header('Location: index.php');
}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Secret Blog升級程序</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body{
			background-color:rgb(225,240,255);
			font-family:"微軟正黑體","新細明體",Arial;
		}
		h2,h3{
			font-weight:100;
		}
		#main{
			width:800px;
			background-color:rgba(255,255,255,0.9);
			margin:2em auto;
			padding: 0.5em 1.5em 1.5em 1.5em;
			box-shadow:0px 0px 20px rgb(210,225,245);
			border-radius:0.5em;
		}
		.message {
			width:80%;
			max-height:500px;
			overflow:auto;
			padding: 1em;
			margin-bottom:1em;
			background: rgb(190, 240 ,190);
			font-size: 90%;
			line-height: 1.5em;
			word-wrap: break-word;
			border-radius:0.25em;
		}
		.radio{
			font-size:96%;
			margin-bottom:2em;
		}
		input[type='text'],input[type='password']{
			max-width:250px;
			display:inline-block;
		}
		fieldset+fieldset{
			margin-top:2em;
		}
	</style>
</head>
<body>
	<div id="main">
		<h2 class="text-center">Secret Blog升級程序</h2>
		<?php if($_step==0){ ?>
		<h3>授權條款</h3>
		<p>本程序僅適用於 Secret Blog 2.0 升級至 Secret Blog 2.1</p>
		<a class="btn btn-primary" href="upgrade.php?step=1">開始升級</a>
		<?php }elseif($_step==1){
			$error = false;
			$errormsg = null;
			
			try {
				require_once('Connections/SQL.php');
				require_once('config.php');
				if(mysqli_connect_errno()){
					$error = true;
					$errormsg = '資料庫連線失敗<br>'.mysqli_connect_error();
				}else{
					$query=explode(';',str_replace("\r",'',str_replace("\n",'',file_get_contents('update.sql'))));
					
					foreach($query as $val){
						if($val!=''){
							$SQL->query($val);
						}
					}
				}
			}
			catch (Exception $e) {
				$error = true;
				$errormsg = base64_encode(json_encode(array(
					'type' => 'SQL Insert Error',
					'line' => __LINE__,
					'file' => dirname(__FILE__) . ';' . __FILE__,
					'errormsg' => $e->getMessage(),
				)));
			}
			
			if($error === false){
		?>
		<h3 class="text-success">升級成功！</h3>
		<p>Secret Blog已升級成功，為了保障您網站的安全，請在此選擇一種方式來處理此程序。</p>
		<form name="form1" method="post" action="upgrade.php?step=2">
			<div class="radio">
				<label>
					<input name="radio" type="radio" value="unlink" checked="checked">刪除此升級程序
				</label>
			</div>
			<div class="radio">
				<label>
					<input name="radio" type="radio" value="rename">重新命名此升級程序
				</label>
			</div>
			<input class="btn btn-primary" type="submit" value="確定！">
		</form>
		<?php } else { ?>
		<h3 class="text-danger">Secret Blog升級失敗！</h3>
		<p>Secret Blog升級時發生錯誤！</p>
		<p>參考代碼：</p>
		<div class="message"><?php echo $errormsg; ?></div>
		<?php }} ?>
	</div>
</body>
</html>