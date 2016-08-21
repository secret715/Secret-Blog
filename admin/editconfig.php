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

if(isset($_POST['site_name'])){
	$config='../config.php';
	$config_sample='../config-sample.php';
	
	$_url_type=sb_post_url_array();
	if(isset($_url_type[$_POST['url_rewrite']])){
		if($_POST['url_rewrite']>0){
			$_url_rewrite=$_POST['url_rewrite'];
			$_rule='RewriteRule ^([A-Za-z]+)'.sprintf($_url_type[$_POST['url_rewrite']],'([0-9]+)').'$ $1.php?id=$2 [L]';		
$_new_htaccess='Options -Indexes
# BEGIN mod_rewrite
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase '.str_replace('https://','',str_replace('http://','',str_replace($_SERVER['HTTP_HOST'],'',str_replace('/admin/','',sb_get_headurl())))).
"\n".$_rule.'
</IfModule>
# END mod_rewrite';
		}else{
			$_new_htaccess='Options -Indexes';
		}
		file_put_contents('../.htaccess',$_new_htaccess/*,FILE_APPEND*/);
	}else{
		$_url_rewrite=0;
	}
	
	$put_config = vsprintf(file_get_contents($config_sample),array(
		addslashes(htmlspecialchars($_POST['site_name'])),
		addslashes(htmlspecialchars($_POST['keyword'])),
		abs($_POST['url_rewrite']),
		addslashes($_POST['description']),
		abs($_POST['list_limit']),
		addslashes($_POST['disqus'])
	));
	file_put_contents($config,$put_config);
	$_ok=true;
	require('../config.php');
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'設定',true);
?>
<div class="main">
<?php if(isset($_ok)){?>
	<div class="alert alert-success">修改成功！</div>
<?php } ?>
<div class="page-header">
	<h2>設定</h2>
</div>
<form class="form-horizontal" name="form1" method="POST" action="editconfig.php">
	<fieldset>
		<legend>主要</legend>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="site_name">網站名稱：</label>
			<div class="col-sm-6">
				<input class="form-control" name="site_name" type="text" value="<?php echo $blog['site_name']; ?>" required="required">
			</div>
			<div class="col-sm-4"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="keyword">網站關鍵字：</label>
			<div class="col-sm-6">
				<input class="form-control" name="keyword" type="text" value="<?php echo $blog['keyword']; ?>">
			</div>
			<div class="col-sm-4 help-block">
				以 , 分割，不可超過 10 個
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="description">網站簡介：</label>
			<div class="col-sm-6">
				<textarea class="form-control" name="description" type="text" value="<?php echo $blog['description']; ?>"></textarea>
			</div>
			<div class="col-sm-4 help-block">
				最多 150 個字 (不可使用HTML)
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>文章</legend>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="url_rewrite">網址格式：</label>
			<div class="col-sm-6">
				<select class="form-control" name="url_rewrite">
				<?php
				$_type=sb_post_url_array();
				for($i=0;$i<count($_type);$i++){
				?>
					<option value="<?php echo $i; ?>"<?php if($blog['url_rewrite']==$i){ echo ' selected="selected"';} ?>>post<?php echo sprintf($_type[$i],'ID'); ?></option>
				<?php } ?>
				</select>
			</div>
			<div class="col-sm-4"></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="list_limit">顯示數量：</label>
			<div class="col-sm-6">
				<div class="input-group">
					<input class="form-control" name="list_limit" type="text" value="<?php echo $blog['list']['limit']; ?>">
					<span class="input-group-addon">篇</span>
				</div>
			</div>
			<div class="col-sm-4 help-block">
				每頁所顯示的文章數量
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="disqus">DISQUS：</label>
			<div class="col-sm-6">
				<textarea class="form-control" name="disqus" type="text"><?php echo $blog['disqus']; ?></textarea>
			</div>
			<div class="col-sm-4 help-block">
				DISQUS程式碼放置
			</div>
		</div>
	</fieldset>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-6">
			<input name="button" type="submit" class="btn btn-success btn-lg" value="修改" />
		</div>
		<div class="col-sm-4"></div>
	</div>
</form>
<?php $view->render(); ?>