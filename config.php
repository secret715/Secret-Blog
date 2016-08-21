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

require_once('include/function.php');
if(!session_id()) {
	session_start();
}

global $blog;

date_default_timezone_set("Asia/Taipei"); //時區設定
$blog['site_name'] = 'Secret Blog'; //網站名稱
$blog['keyword']='Secret Blog';//網站關鍵字  以 , 區隔  不可超過 10個
$blog['url_rewrite']='0';//是否啟用網址重寫功能(需修改.htaccess)
$blog['description']='Secret Blog';//網站簡介  最多 150 個字
$blog['list']['limit']='10';//首頁顯示文章數量
$blog['disqus']=
<<<DISQUS
Disqus程式碼
DISQUS;
//DISQUS程式碼放置區