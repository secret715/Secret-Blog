(function($,window){
	function sb_autosave(p){
		$.ajax({
			url:'../include/ajax/autosave.php',
			type:'GET',
			dataType: 'json',
			success: function(data){
				if(data!=''){
					if(data['login']==false){
						$.ajax({
							url:'../include/ajax/autosave.php',
							type:'POST',
							dataType: 'json',
							data:{'p':p,'data':CKEDITOR.instances.content.getData()},
							success: function(data){
								if(data!=''){
									if(data['save']==true){
										alert('您已被登出，請重新登入');
										location.href='index.php';
									}
								}
							}
						});
					}
					setTimeout(function(){sb_autosave(p)},30000);
				}
			}
		});
	}

	window.sb_autosave = sb_autosave;
})(jQuery,window);