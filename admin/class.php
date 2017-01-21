<?php
/*
<Secret Blog>
Copyright (C) 2012-2017 太陽部落格站長 Secret <http://gdsecret.com>

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

if(isset($_GET['del'])&& trim($_GET['del'])){

	$SQL->query("DELETE FROM `class` WHERE `id`='%d'",array($_GET['del']));
	$_delok=true;
	
}elseif(isset($_POST['class']) && trim($_POST['class'])!=''){

	$SQL->query("INSERT INTO `class` (`classname`,`mktime`) VALUES ('%s',now())",array(htmlspecialchars($_POST['class'])));
	$_new=true;
	
}elseif(isset($_GET['edit']) && trim($_GET['edit'])!=''){
	$edit=sb_get_result("SELECT * FROM `class` WHERE `id`='%d'",array(abs($_GET['edit'])));
	if($edit['num_rows']>0){
		$SQL->query("UPDATE `class` SET `classname`='%s' WHERE `id`='%d'",array(htmlspecialchars($_POST['edit']),$edit['row']['id']));
		$_save=true;
	}
}


if(isset($_GET['id'])&& trim($_GET['id'])!=''){
	$class=sb_get_result("SELECT * FROM `class` WHERE `id`=%d",array(abs($_GET['id'])));
	if($class['num_rows']<1){
		header("Location: class.php");
	}
}

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
	$class_list=sb_get_result("SELECT * FROM `class` ORDER BY `mktime` ASC LIMIT %d,20",array($limit_start));
} else {
	$limit_start = 0;
	$class_list=sb_get_result("SELECT * FROM `class` ORDER BY `mktime` ASC LIMIT %d,20",array($limit_start));
}

$all_class=sb_get_result("SELECT COUNT(*) FROM `class`");

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'分類',true);

if(isset($_delok)){
?>
<div class="alert alert-success">
	刪除成功！
</div>
<?php }elseif(isset($_new)){ ?>
<div class="alert alert-success">
	新增成功！
</div>
<?php }elseif(isset($_save)){ ?>
<div class="alert alert-success">
	編輯成功！
</div>
<?php } ?>
<div class="page-header">
	<h2>分類</h2>
</div>
<div class="row" style="margin-bottom:2em;">
	<div class="col-md-5">
		<form class="form-inline" action="class.php" method="POST">
		<legend>新增分類</legend>
			<div class="form-group">
				<input class="form-control" name="class" type="text" placeholder="名稱" required="required">
			</div>
			<input type="submit" class="btn btn-success" value="新增">
		</form>
	</div>
	<div class="col-md-7">
		<?php if(isset($class)){ ?>
		<form class="form-inline" action="class.php?edit=<?php echo $_GET['id'];?>" method="POST">
		<legend>編輯分類</legend>
			<div class="form-group">
				<input class="form-control" name="edit" type="text" placeholder="名稱" value="<?php echo stripslashes($class['row']['classname']); ?>" required="required">
			</div>
			<input type="submit" class="btn btn-primary" value="儲存">
		</form>
		<?php } ?>
	</div>
</div>
<?php if($class_list['num_rows']>0){ ?>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>分類名稱</th>
			<th>文章篇數</th>
			<th>管理</th>
		</tr>
	</thead>
	<tbody>
	<?php
	do{
		$post_count=implode('',$SQL->query("SELECT COUNT(*) FROM `post` WHERE `type` =0 AND `class`='%d'",array($class_list['row']['id']))->fetch_assoc());
	?>
		<tr>
			<td><?php echo $class_list['row']['classname']; ?></td>
			<td><?php echo $post_count; ?></td>
			<td><a href="class.php?id=<?php echo $class_list['row']['id']; ?>" class="btn btn-primary">編輯</a>&nbsp;<a href="class.php?del=<?php echo $class_list['row']['id']; ?>" class="btn btn-danger">刪除</a></td>
		</tr>
	<?php
	}while($class_list['row'] = $class_list['query']->fetch_assoc());
	?>
	</tbody>
</table>
<?php echo sb_page_pagination('class.php',@$_GET['page'],implode('',$all_class['row']),20); ?>
<?php }else{ ?>
<p class="text-center text-danger" style="font-size:150%;">沒有分類！</p>
<?php } ?>
<?php $view->render(); ?>