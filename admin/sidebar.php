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

set_include_path('../include/');
$includepath = true;

require_once('../Connections/SQL.php');
require_once('../config.php');
require_once('view.php');

if(!isset($_SESSION['Blog_Username']) or $_SESSION['Blog_UserGroup']<5){
	header("Location: ../index.php");
	exit;
}

$_style=array('default','primary','success','info','warning','danger');

if(isset($_POST['title'])&&isset($_POST['content'])&&isset($_POST['class'])&&isset($_POST['show'])&&(count($_POST['title'])==count($_POST['content']))&&(count($_POST['title'])==count($_POST['class']))&&(count($_POST['title'])==count($_POST['show']))){
	$_data=array();
	$i=0;
	foreach($_POST['content'] as $_k => $_v){
		if($_v==''){
			continue;
		}
		if($_POST['show'][$_k]==0){
			$_POST['show'][$_k]=0;
		}else{
			$_POST['show'][$_k]=1;
		}
		//$_data[]=array('標題','內容','樣式','是否顯示');
		$_data[]=array(htmlspecialchars($_POST['title'][$_k]),htmlspecialchars($_v),intval($_POST['class'][$_k]),$_POST['show'][$_k]);
		$i++;
	}
	file_put_contents('../include/sidebar.json',json_encode($_data));
	
	
	$_modal='';
	foreach ($_data as $_v){
		if(!$_v[3])continue;
		$_modal.='<div class="panel panel-'.$_style[$_v[2]].'">';
		if($_v[0]!='')$_modal.='<div class="panel-heading"><h3 class="panel-title">'.htmlspecialchars($_v[0]).'</h3></div>';
		$_modal.='<div class="panel-body">'.nl2br(htmlspecialchars($_v[1])).'</div></div>';
	}
	file_put_contents('../include/sidebar.php',$_modal);
	$_ok=true;
}


$view = new View('theme/admin_default.html','admin/nav.php','',$blog['site_name'],'側邊欄',true);
?>
<style>
.item{
	margin-bottom:30px;
	padding:10px;
	border-bottom:1px solid rgb(200,200,200);
}
.item .btn-group{
	margin-bottom:-20px;
}
</style>
<script>
$(function(){
	style_type=["<?php echo implode('","',$_style); ?>"];
	data=<?php echo file_get_contents('../include/sidebar.json'); ?>;
	
	function sidebar_modal(title,content,style,show){
		var show_check='';
		if(show)show_check=' checked';
		
		var class_option='';
		for(var n in style_type){
			if(style==n){
				class_option+='<option value="'+n+'" selected>'+style_type[n]+'</option>';
			}else{
				class_option+='<option value="'+n+'">'+style_type[n]+'</option>';
			}
		}
		var modal='<div class="item"><div class="control text-right"><div class="btn-group"><span class="btn btn-default go-up"><span class="glyphicon glyphicon-chevron-up"></span></span> <span class="btn btn-default go-down"><span class="glyphicon glyphicon-chevron-down"></span></span> <span class="btn btn-danger remove"><span class="glyphicon glyphicon glyphicon-trash"></span></span></div></div><div class="form-group"><label for="title[]">標題</label><input name="title[]" class="form-control" type="text" value="'+title+'"></div><div class="form-group"><label for="content[]">內容</label><textarea name="content[]" class="form-control" rows="3" required>'+content+'</textarea></div><div class="form-group"><label for="class[]">樣式</label><select name="class[]" class="form-control">'+class_option+'</select></div><div class="form-group"><label for="show[]">顯示</label><input name="show[]" type="checkbox" value="1"'+show_check+'></div></div>';
		return modal;
	}
	
	
	html='';
	for (i in data){
		html+=sidebar_modal(data[i][0],data[i][1],data[i][2],data[i][3]);
	}
	$('#data').append(html);
	
	
	function sort(m,n){
		if($('.item').length<1){
			return false;
		}
		
		a=$('.item').eq(m);
		if(n==0){
			a.insertAfter($('.item').eq(parseInt(m)+parseInt(1)));
		}else if(parseInt(m)-parseInt(1)>=0){
			a.insertBefore($('.item').eq(parseInt(m)-parseInt(1)));
		}
	}
	$(document.body).on('click','.go-up',function(e){
		e.preventDefault();
		sort($(this).parent().parent().parent().index(),1);
	});
	
	$(document.body).on('click','.go-down',function(e){
		e.preventDefault();
		sort($(this).parent().parent().parent().index(),0);
	});
	
	$(document.body).on('click','.remove',function(e){
		e.preventDefault();
		$(this).parent().parent().parent().remove();
	});
	
	$(document.body).on('click','#add',function(e){
		e.preventDefault();
		$('#data').append(sidebar_modal('','',0,1));
		$('html').animate({
			scrollTop: $('#data .item:last').offset().top
		}, 500);
	});
	
	$(document.body).on('submit','form',function(){
		$('input[name="show[]"]').each(function(){
			if ($(this).prop('checked') == false)$(this).prop('checked',true).val(0);
		});
	});
});
</script>
<?php if(isset($_ok)){ ?>
<div class="alert alert-success">修改成功！</div>
<?php } ?>
<div class="page-header">
	<h2>側邊欄</h2>
</div>
<form method="POST">
	<span id="add" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> 新增</span>
	<div id="data"></div>
	<input name="button" type="submit" class="btn btn-success" value="修改">
</form>
<?php $view->render(); ?>