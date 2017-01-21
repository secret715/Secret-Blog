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

require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_GET['id'])or trim($_GET['id'])==''){
	header('Location: index.php');
}

$page=sb_get_result("SELECT * FROM `post` WHERE `id`=%d AND `type`=1 AND `public`=1",array(abs($_GET['id'])));//type 0 =文章  type 1= 頁面      public 1=公開   public 0=私密

if($page['num_rows']<1){
	header('Location: index.php');
}

$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($page['row']['author']));

$view = new View('include/theme/default.html','include/nav.php','include/sidebar.php',$blog['site_name'],$page['row']['title']);
$view->addMeta('author',$author['row']['nickname']);
$view->addMeta('description',sb_post_description($page['row']['content'],60));
$view->addMeta('keyword',$page['row']['keyword']);
?>
<article class="post">
	<h2 class="post_title" itemprop="name"><?php echo $page['row']['title']; ?></h2>
	<div class="content" itemprop="articleBody">
	<?php echo stripslashes($page['row']['content']); ?>
	</div>
	
	<ul class="list-inline">
		<li>作者：<?php echo $author['row']['nickname']; ?></li>
		<li>在 <span itemprop="datePublished"><?php echo date('Y-m-d',strtotime($page['row']['mktime'])); ?></span> 發布</li>
    </ul>
</article>
<?php $view->render(); ?>