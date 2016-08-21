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

$list = sb_get_result("SELECT * FROM `post` WHERE `public`=1 ORDER BY `id` DESC");

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
		<loc><?php echo sb_get_headurl(); ?></loc>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>
<?php
if($list['num_rows']>0){
	do{
		if($list['row']['type']==1){
			$url=sb_post_url($list['row']['id'],'page',$blog['url_rewrite']);
			$priority=0.7;
		}else{
			$url=sb_post_url($list['row']['id'],'post',$blog['url_rewrite']);
			$priority=0.8;
		}
?>
    <url>
		<loc><?php echo $url; ?></loc>
		<priority><?php echo $priority; ?></priority>
	</url>
<?php
	}while($list['row'] = $list['query']->fetch_assoc());
}
?>
</urlset>