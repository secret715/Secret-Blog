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

if(isset($_GET['del'])){
	file_put_contents('error.php','');
}

$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'安全',true);
?>
<div class="page-header">
	<h2>安全</h2>
</div>
<?php if(filesize('error.php')>5){ ?>
<p><a href="security.php?del">清空所有紀錄</a></p>
<?php
	echo nl2br(file_get_contents('error.php'));
}else{
	echo '沒有錯誤登入！';
}
$view->render(); ?>