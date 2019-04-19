<?php
/*
<Secret Blog>
Copyright (C) 2012-2019 Secret <http://gdsecret.com>

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
	exit;
}
 
if(!isset($_SESSION['Blog_Auth'])){
	$_SESSION['Blog_Auth']=mt_rand(100,99999);
}
 
$post=sb_get_result("SELECT * FROM `post` WHERE `id`='%d' AND `type`=0",array(abs($_GET['id'])));//type 0 =文章  type 1= 頁面      public 1=公開   public 0=私密

switch ($post['row']['public']){
	case 0:
		unset($post['num_rows']);
		break;
	case 2:
		unset($post['num_rows']);
		break;
	case 3:
		if(!isset($_SESSION['Blog_Username'])){
			unset($post['num_rows']);
		}
		break;
}


if(!isset($post['num_rows'])){
	header('Location: index.php');
	exit;
}

$class=sb_get_result("SELECT * FROM `class` WHERE `id` =%d",array($post['row']['class']));


$author=sb_get_result("SELECT `nickname` FROM `member` WHERE `id`=%d",array($post['row']['author']));

$more_post=sb_get_result("SELECT * FROM `post` WHERE `id`!=%d AND `type`=0 AND `public`=1 AND `class` = '%d' ORDER BY RAND() LIMIT 0,3",array(abs($_GET['id']),$post['row']['class']));



$_member=0;
if(isset($_SESSION['Blog_Username'])){
	$_member=$_SESSION['Blog_Id'];
	$_POST['name']='';
	$_POST['email']='';
}
if(isset($_POST['name'])&&isset($_POST['content'])&&isset($_POST['email'])&&(($_POST['email']!=''&&filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)&&$_POST['content']!=''&&$_POST['name']!='')or(isset($_SESSION['Blog_Username'])))&&$post['row']['comment']==true&&isset($_GET[$_SESSION['Blog_Auth']])){
	
	if(strtoupper($_POST['captcha']) != strtoupper($_SESSION['captcha'])){
		echo json_encode(array('status'=>'captcha'));
		exit;
	}
	
	$_POST['name']=htmlspecialchars($_POST['name']);
	$_POST['content']=mb_substr(htmlspecialchars($_POST['content']),0,150,'utf-8');
	
	$_parent=0;
	if(isset($_GET['parent'])){
		$_parent=intval($_GET['parent']);
		$_auth=sb_get_result("SELECT `id` FROM `comment` WHERE `id`='%d' AND `parent`='0'",array($_parent));
		if($_auth['num_rows']==0){
			$_parent=0;
		}
	}
	
	$SQL->query("INSERT INTO `comment` (`post`, `parent`, `content`, `mktime`, `author`, `email`, `member`) VALUES ('%d', '%d', '%s',  now(),'%s', '%s', '%d')",array($post['row']['id'],$_parent,$_POST['content'],$_POST['name'],$_POST['email'],$_member));
	
	echo json_encode(array('status'=>'success'));
	die;
}elseif(isset($_GET['comment'])){
	$comment=sb_get_result("SELECT * FROM `comment` WHERE `post`='%d' AND `parent`='0' ORDER BY `mktime` DESC",array(abs($_GET['id'])));
	if($comment['num_rows']>0){
		do{
			echo sb_comment_list($comment,$post['row']['id'],$post['row']['comment']);
		}while($comment['row'] = $comment['query']->fetch_assoc());
	}
	die;
}


$view = new View('include/theme/default.html','include/nav.php','include/sidebar.php',$blog['site_name'],$post['row']['title']);
$view->addMeta('author',$author['row']['nickname']);
$view->addMeta('description',sb_post_description($post['row']['content'],60));
$view->addMeta('keyword',$post['row']['keyword']);
?>
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
<?php $view->render(); ?>