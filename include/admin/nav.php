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
 if(isset($_SESSION['Blog_Username'])){ ?>
	<li class="dropdown">
		<a href="post.php" data-target="#" data-toggle="dropdown">文章</a>
		<ul class="dropdown-menu">
			<li><a href="post.php">所有文章</a></li>
			<li><a href="newpost.php">新增文章</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="class.php" data-target="#" data-toggle="dropdown">分類</a>
		<ul class="dropdown-menu">
			<li><a href="class.php">所有分類</a></li>
			<li><a href="class.php">新增分類</a></li>
		</ul>
	</li>
	<?php if($_SESSION['Blog_UserGroup']>=5){ ?>
	<li class="dropdown">
		<a href="page.php" data-target="#" data-toggle="dropdown">頁面</a>
		<ul class="dropdown-menu">
			<li><a href="page.php">所有頁面</a></li>
			<li><a href="newpage.php">新增頁面</a></li>
		</ul>
	</li>
	<li><a href="http://disqus.com/admin/" target="_blank">Disqus</a></li>
	<?php } ?>
	<li class="dropdown">
		<a href="account.php" data-target="#" data-toggle="dropdown">帳號</a>
		<ul class="dropdown-menu">
			<li><a href="editaccount.php">我的帳號</a></li>
			<?php if($_SESSION['Blog_UserGroup']>=9){ ?>
			<li><a href="account.php">所有帳號</a></li>
			<li><a href="newaccount.php">新增帳號</a></li>
			<?php } ?>
		</ul>
	</li>
	<?php if($_SESSION['Blog_UserGroup']>=9){ ?>
	<li class="dropdown">
		<a href="editconfig.php" data-target="#" data-toggle="dropdown">系統</a>
		<ul class="dropdown-menu">
			<li><a href="editconfig.php">設定</a></li>
			<li><a href="security.php">安全</a></li>
		</ul>
	</li>
	<?php } ?>
	<li><a href="index.php?out">登出</a></li>
<?php } ?>