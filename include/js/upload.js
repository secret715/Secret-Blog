(function($,window){
	function sb_filemanager(p){
		$(document.body).on('click','.file_remove',function(){
			var $t = $(this);
			$.ajax({
				url:$t.attr('data-url'),
				dataType: 'text',
				success: function(data){
					if(data==1){
						$t.parent().parent().remove();
					}
				}
			});
		});
		$('#filemanager_btn').click(function(){
			$.ajax({
				url:'../include/ajax/upload.php?list&p='+p,
				dataType: 'html',
				success: function(data){
					if(data!=''){
						$('#filemanager .modal-body').html(data);
					}
				}
			});
		});
	}
	
	function sb_uploader(p,max_file_size){
		//max_file_size單位為byte
		var allow_ext=['png','gif','jpg','zip','pdf','doc','ppt','xls','odt','odp','ods'];
		$('#uploadinfo').hide();
		$('#fileupload').fileupload({
			dropZone: $('#drop'),
			url: '../include/ajax/upload.php?p='+p,
			dataType: 'json',
			add: function (e, data) {
					$('#uploadinfo').show();
					var tpl = $('<tr class="warning"><td class="file-cancel"><span class="glyphicon glyphicon-remove"></span></td><td class="file-info"></td><td class="file-url"></td><td class="file-pro"></td></tr>');
					tpl.find('.file-info').text(data.files[0].name);
					data.context = tpl.appendTo($('.item'));
					
					$('#progress .bar').width(0).text('');
					
					var extend=data.files[0].name.split('.').pop();
					
					for(var i=0;i<allow_ext.length;i++){
						if(allow_ext[i]==extend){
							var in_array = true;
							break;
						}else{
							var in_array = false;
						}
					}
					
					var error=0;
					if(in_array==false){
						alert(data.files[0].name+' 不允許此格式');
						error=1;
					}
					if(data.files[0].size>max_file_size){
						alert(data.files[0].name+' 的大小過大');
						error=1;
					}
					if(error==0){
						var jqXHR = data.submit();
					}else{
						tpl.remove();
					}
					
					tpl.find('.file-cancel').click(function(){
						tpl.fadeOut(function(){
							if(tpl.hasClass('warning')){
								jqXHR.abort();  //終止上傳
							}
							tpl.remove();
						});
					});
				},

				//單一檔案進度
				progress: function(e, data){
					var progress = parseInt(data.loaded / data.total * 100, 10);
					data.context.find('.file-pro').text(progress+'%').change();
					if(progress == 100){
						data.context.addClass('success').removeClass('warning');
						data.context.find('.file-pro').text('完成');
					}
				},

				//總進度
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$('#progress .progress-bar').css('width', progress + '%');
					$('#progress .progress-bar').text(progress + '%');
				},

				//上傳失敗
				fail:function(e, data){
					data.context.addClass('danger');
					data.context.find('.file-pro').text();
				},

				//單一檔案上傳完成
				done: function (e, data) {
					if(data.result.status=='error'){
						data.context.addClass('danger');
						data.context.find('.file-pro').text(data.result.msg);
					}else if(data.result.id!=''){
						data.context.find('.file-url').html('<small><a href="'+data.result.url+'" target="_blank">'+data.result.url+'</a></small>');
					}
				}
		});
		$("#drop").bind({
			dragenter: function() {
				$(this).addClass('active');
			},
			dragleave: function() {
				$(this).removeClass('active');
			}
		});
	}

	window.sb_filemanager = sb_filemanager;
	window.sb_uploader = sb_uploader;
})(jQuery,window);