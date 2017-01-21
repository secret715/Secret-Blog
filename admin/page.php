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

if(!isset($_SESSION['Blog_Username']) or $_SESSION['Blog_UserGroup']<5){
	header("Location: ../index.php");
	exit;
}

if(isset($_GET['del'])&& trim($_GET['del'])){
	$SQL->query("DELETE FROM `post` WHERE `id`='%d'",array($_GET['del']));
	@sb_deletedir('../upload/'.abs($_GET['del']));
	$_delok=true;
}

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
	$page_list=sb_get_result("SELECT * FROM `post` WHERE `type` =1 ORDER BY `mktime` DESC LIMIT %d,20",array($limit_start));
} else {
	$limit_start = 0;
	$page_list=sb_get_result("SELECT * FROM `post` WHERE `type` =1 ORDER BY `mktime` DESC LIMIT %d,20",array($limit_start));
}//type 0 =文章  type 1= 頁面      public 1=公開   public 0=私密

$all_page=sb_get_result("SELECT COUNT(*) FROM `post` WHERE `type` =1");

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'頁面',true);

if(isset($_delok)){
?>
<div class="alert alert-success">
	成功刪除頁面！
</div>
<?php }elseif(isset($_GET['newpage'])){ ?>
<div class="alert alert-success">
	新增成功！
</div>
<?php }if($page_list['num_rows']>0){ ?>
<div class="page-header">
	<h2>頁面</h2>
</div>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>標題</th>
			<th>作者</th>
			<th>日期</th>
			<th>管理</th>
		</tr>
	</thead>
	<tbody>
	<?php
	do{
		$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($page_list['row']['author']));
	?>
		<tr>
			<td><?php echo $page_list['row']['title'].'&nbsp;&nbsp;&nbsp;'.sb_post_public($page_list['row']['public']); ?></td>
			<td><?php echo $author['row']['nickname']; ?></td>
			<td><?php echo date('Y-m-d',strtotime($page_list['row']['mktime'])); ?></td>
			<td><a href="editpage.php?id=<?php echo $page_list['row']['id']; ?>" class="btn btn-primary">編輯</a>&nbsp;<a href="page.php?del=<?php echo $page_list['row']['id']; ?>" class="btn btn-danger">刪除</a></td>
		</tr>
	<?php
	}while($page_list['row'] = $page_list['query']->fetch_assoc());
	?>
	</tbody>
</table>
<?php echo sb_page_pagination('page.php',@$_GET['page'],implode('',$all_page['row']),20); ?>
<?php }else{ ?>
<p class="text-center text-danger" style="font-size:150%;">沒有頁面！</p>
<?php } ?>
<?php $view->render(); ?>