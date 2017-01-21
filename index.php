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

if(isset($_GET['page'])){
	$limit_start = abs(intval(($_GET['page']-1)*$blog['list']['limit']));
	$post_list=sb_get_result("SELECT * FROM `post` WHERE `type` =0 AND `public`=1 ORDER BY `mktime` DESC LIMIT %d,%d",array($limit_start,$blog['list']['limit']));
} else {
	$limit_start = 0;
	$post_list=sb_get_result("SELECT * FROM `post` WHERE `type` =0 AND `public`=1 ORDER BY `mktime` DESC LIMIT %d,%d",array($limit_start,$blog['list']['limit']));
}//type 0 =文章  type 1= 頁面      public 1=公開   public 0=私密

$all_post=sb_get_result("SELECT COUNT(*) FROM `post` WHERE `type` =0 AND `public`=1");

$view = new View('include/theme/default.html','include/nav.php','include/sidebar.php',$blog['site_name'],'首頁');
if($blog['description']!=''){
	$view->addMeta('description',$blog['description']);
}
if($blog['keyword']!=''){
	$view->addMeta('keyword',$blog['keyword']);
}

if($post_list['num_rows']>0){
	do{
	$class=sb_get_result("SELECT * FROM `class` WHERE `id` =%d",array($post_list['row']['class']));
	$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($post_list['row']['author']));
?>
<article class="post post_list">
	<h2 class="post_title"><a href="<?php echo sb_post_url($post_list['row']['id'],'post',$blog['url_rewrite']); ?>"><?php echo $post_list['row']['title']; ?></a></h2>
	<div class="content">
	<?php
	$_gist=sb_post_gist($post_list['row']['content'],'<!--more-->');
	if($_gist){
		echo $_gist.'<p class="more-link"><a href="'.sb_post_url($post_list['row']['id'],'post',$blog['url_rewrite']).'" class="btn btn-primary"> 繼續閱讀 → </a></p>';
	}else{
		echo stripslashes($post_list['row']['content']);
	}
	?>
	</div>
	<ul class="list-inline">
		<li>作者：<?php echo $author['row']['nickname']; ?></li>
		<li>在 <?php echo date('Y-m-d',strtotime($post_list['row']['mktime'])); ?> 發布</li>
		<li>分類：<a href="<?php echo sb_get_headurl(); ?>class.php?id=<?php echo $post_list['row']['class']; ?>"><?php echo $class['row']['classname']; ?></a></li>
    </ul>
</article>
<?php
	}while($post_list['row'] = $post_list['query']->fetch_assoc());
	echo sb_page_pagination('index.php',@$_GET['page'],implode('',$all_post['row']),$blog['list']['limit']);
}
	$view->render();
?>