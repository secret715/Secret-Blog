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

$post_list=sb_get_result("SELECT * FROM `post` WHERE `type` =0 AND `public`=1 ORDER BY `mktime` DESC LIMIT 0,%d",array($blog['list']['limit']));

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0">
  <channel>
    <title><?php echo $blog['site_name']; ?></title>
    <link><?php echo sb_get_headurl(); ?></link>
    <!--description></description-->
    <language>Zh-TW</language>
    <pubDate><?php echo date('r'); ?></pubDate>
    <lastBuildDate><?php echo date('r'); ?></lastBuildDate>
    <generator>Secret Blog</generator>
<?php
if($post_list['num_rows']>0){
	do{
?>
    <item>
      <title><?php echo $post_list['row']['title']; ?></title>
      <link><?php echo sb_post_url($post_list['row']['id'],'post',$blog['url_rewrite']); ?></link>
      <description><![CDATA[
		<?php
		$_gist=sb_post_gist($post_list['row']['content'],'<!--more-->');
		if($_gist){
			echo $_gist.'<p class="more-link"><a href="'.sb_post_url($post_list['row']['id'],'post',$blog['url_rewrite']).'" class="btn btn-primary"> 繼續閱讀 → </a></p>';
		}else{
			echo stripslashes($post_list['row']['content']);
		}
		?>
		]]></description>
      <pubDate><?php echo date('r',strtotime($post_list['row']['mktime'])); ?></pubDate>
      <guid><?php echo sb_post_url($post_list['row']['id'],'post',$blog['url_rewrite']); ?></guid>
    </item>
<?php
	}while($post_list['row'] = $post_list['query']->fetch_assoc());
}
?>
  </channel>
</rss>