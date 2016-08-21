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

require_once('Connections/SQL.php');
require_once('config.php');
require_once('include/view.php');

if(!isset($_GET['id'])or trim($_GET['id'])==''){
	header('Location: index.php');
}

$post=sb_get_result("SELECT * FROM `post` WHERE `id`=%d AND `type`=0 AND `public`=1",array(abs($_GET['id'])));//type 0 =文章  type 1= 頁面      public 1=公開   public 0=私密

$class=sb_get_result("SELECT * FROM `class` WHERE `id` =%d",array($post['row']['class']));


if($post['num_rows']<1){
	header('Location: index.php');
}

$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($post['row']['author']));

$more_post=sb_get_result("SELECT * FROM `post` WHERE `id`!=%d AND `type`=0 AND `public`=1 AND `class` = '%d' ORDER BY RAND() LIMIT 0,3",array(abs($_GET['id']),$post['row']['class']));

$view = new View('include/theme/default.html','include/nav.php','include/sidebar.php',$blog['site_name'],$post['row']['title']);
$view->addMeta('author',$author['row']['nickname']);
$view->addMeta('description',sb_post_description($post['row']['content'],60));
$view->addMeta('keyword',$post['row']['keyword']);
?>
<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
<script>
$(function(){
	$('pre').addClass('prettyprint linenums');
	prettyPrint();
});
</script>
<article class="post" itemscope itemtype="http://schema.org/Article">
	<h2 class="post_title" itemprop="name"><?php echo $post['row']['title']; ?></h2>
	<div class="content" itemprop="articleBody">
	<?php echo stripslashes($post['row']['content']); ?>
	</div>
	<?php if($more_post['num_rows']>0){ ?>
	<div id="more_post" style="clear:both;">
	更多文章：<br>
	<?php do{ ?>
	<a href="<?php echo sb_post_url($more_post['row']['id'],'post',$blog['url_rewrite']); ?>"><?php echo $more_post['row']['title']; ?></a><br>
	<?php }while($more_post['row'] =$more_post['query']->fetch_assoc()); ?>
	</div>
	<?php } ?>
	
	<ul class="list-inline">
		<li>作者：<?php echo $author['row']['nickname']; ?></li>
		<li>在 <span itemprop="datePublished"><?php echo date('Y-m-d',strtotime($post['row']['mktime'])); ?></span> 發布</li>
		<li>分類：<a href="<?php echo sb_get_headurl(); ?>class.php?id=<?php echo $post['row']['class']; ?>"><?php echo $class['row']['classname']; ?></a></li>
    </ul>
</article>
<!--Disqus程式碼-->
<?php echo $blog['disqus']; ?>
<!--Disqus程式碼 結束-->
<?php $view->render(); ?>