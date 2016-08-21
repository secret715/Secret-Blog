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

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*20));
	$member_list=sb_get_result("SELECT * FROM `member` ORDER BY `joined` ASC LIMIT %d,20",array($limit_start));
} else {
	$limit_start = 0;
	$member_list=sb_get_result("SELECT * FROM `member` ORDER BY `joined` ASC LIMIT %d,20",array($limit_start));
}

$all_member=sb_get_result("SELECT COUNT(*) FROM `member`");

if(isset($_GET['del'])&& trim($_GET['del']) && implode('',$all_member['row'])>1 && $_GET['del']!=$_SESSION['Blog_Id']){
	$SQL->query("DELETE FROM `member` WHERE `id`='%d'",array($_GET['del']));
	$_delok=true;
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'帳號',true);

if(isset($_delok)){
?>
<div class="alert alert-success">
	成功刪除帳號！
</div>
<?php }elseif(isset($_GET['newmember'])){ ?>
<div class="alert alert-success">
	新增成功！
</div>
<?php }if($member_list['num_rows']>0){ ?>
<div class="page-header">
	<h2>帳號</h2>
</div>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th width="20%">帳號</th>
			<th width="20%">暱稱</th>
			<th width="20%">電子信箱</th>
			<th width="10%">權限</th>
			<th width="15%">最後登入</th>
			<th>管理</th>
		</tr>
	</thead>
	<tbody>
	<?php
	do{
	?>
		<tr>
			<td><?php echo $member_list['row']['username']; ?></td>
			<td><?php echo $member_list['row']['nickname']; ?></td>
			<td><?php echo $member_list['row']['email']; ?></td>
			<td><?php echo sb_member_level($member_list['row']['level']); ?></td>
			<td><small><?php echo date('Y-m-d H:i',strtotime($member_list['row']['last_login'])); ?></small></td>
			<td>
			<a href="editaccount.php?id=<?php echo $member_list['row']['id']; ?>" class="btn btn-primary">編輯</a>&nbsp;
			<?php if(implode('',$all_member['row'])>1 && $member_list['row']['id']!=$_SESSION['Blog_Id']){ ?>
				<a href="account.php?del=<?php echo $member_list['row']['id']; ?>" class="btn btn-danger">刪除</a>
			<?php } ?>
			</td>
		</tr>
	<?php
	}while($member_list['row'] = $member_list['query']->fetch_assoc());
	?>
	</tbody>
</table>
<?php echo sb_page_pagination('account.php',@$_GET['page'],implode('',$all_member['row']),20); ?>
<?php }else{ ?>
<p class="text-center text-danger" style="font-size:150%;">沒有帳號！</p>
<?php } ?>
<?php $view->render(); ?>