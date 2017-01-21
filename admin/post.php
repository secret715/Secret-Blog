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
	if($_SESSION['Blog_UserGroup']<5){
		$SQL->query("DELETE FROM `post` WHERE `id`='%d' AND `author` = '%d'",array($_GET['del'],$_SESSION['Blog_Id']));
	}else{
		$SQL->query("DELETE FROM `post` WHERE `id`='%d'",array($_GET['del']));
	}
	@sb_deletedir('../upload/'.abs($_GET['del']));
	$_delok=true;
}

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
} else {
	$limit_start = 0;
}

if($_SESSION['Blog_UserGroup']<5){
	$post_list=sb_get_result("SELECT * FROM `post` WHERE `type` =0 AND `author`='%d' ORDER BY `mktime` DESC LIMIT %d,20",array($_SESSION['Blog_Id'],$limit_start));
	$all_post=sb_get_result("SELECT COUNT(*) FROM `post` WHERE `type` =0 AND `author`='%d'",array($_SESSION['Blog_Id']));
}else{
	$post_list=sb_get_result("SELECT * FROM `post` WHERE `type` =0 AND (`public`!=0 OR `author`='%d') ORDER BY `mktime` DESC LIMIT %d,20",array($_SESSION['Blog_Id'],$limit_start));
	$all_post=sb_get_result("SELECT COUNT(*) FROM `post` WHERE `type` =0 AND (`public`!=0 OR `author`='%d')",array($_SESSION['Blog_Id']));
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'文章',true);

if(isset($_delok)){
?>
<div class="alert alert-success">
	成功刪除文章！
</div>
<?php }elseif(isset($_GET['newpost'])){ ?>
<div class="alert alert-success">
	新增成功！
</div>
<?php }if($post_list['num_rows']>0){ ?>
<div class="page-header">
	<h2>文章</h2>
</div>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>標題</th>
			<th>作者</th>
			<th>分類</th>
			<th>日期</th>
			<th>管理</th>
		</tr>
	</thead>
	<tbody>
	<?php
	do{
		$class=sb_get_result("SELECT * FROM `class` WHERE `id` =%d",array($post_list['row']['class']));
		$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($post_list['row']['author']));
	?>
		<tr>
			<td><?php echo $post_list['row']['title'].'&nbsp;&nbsp;&nbsp;'.sb_post_public($post_list['row']['public']); ?></td>
			<td><?php echo $author['row']['nickname']; ?></td>
			<td><?php echo $class['row']['classname']; ?></td>
			<td><?php echo date('Y-m-d',strtotime($post_list['row']['mktime'])); ?></td>
			<td><a href="editpost.php?id=<?php echo $post_list['row']['id']; ?>" class="btn btn-primary">編輯</a>&nbsp;<a href="post.php?del=<?php echo $post_list['row']['id']; ?>" class="btn btn-danger">刪除</a></td>
		</tr>
	<?php
	}while($post_list['row'] = $post_list['query']->fetch_assoc());
	?>
	</tbody>
</table>
<?php echo sb_page_pagination('post.php',@$_GET['page'],implode('',$all_post['row']),20); ?>
<?php }else{ ?>
<p class="text-center text-danger" style="font-size:150%;">沒有文章！</p>
<?php } ?>
<?php $view->render(); ?>