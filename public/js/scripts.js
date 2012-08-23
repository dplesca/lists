// execute function when the page is ready
Zepto(function($){
	
	$('#listTitle').on('keydown', function(e){
		if (e.keyCode == 13){
			$.post('/ajax', { 'title' : $(this).val(), 'action' : 'new_list' }, function(response){
				if (response.status == 1){					
					window.history.pushState({}, "page 2", "/edit/" + response.list);		
					$('.list_title').html( $('#listTitle').val() );
					$('.add-list').addClass('hidden');
					$('#list_id').val(response.list);
					$('.list-components.hidden').removeClass('hidden');
					$('#list_item').focus();				
				}
			}, 'json');
		}		
	});

	$('#list_item').on('keyup', function(e){
		if (e.keyCode == 188){
			var value = $(this).val().replace(',', '');
			if (value){
				
				$.post('/ajax', { 'item' : value, 'action' : 'new_item', 'list_id' : $('#list_id').val() }, function(response){ 
					if (response.status == 1){
						$('.components').prepend('<div class="list-component" data-id="' + response.item + '">' + value + '</div>');
					}
				}, 'json');
			}
			$(this).val('');
		}
	});

	$('.list-component').on('swipeRight', function(e){
		var lcom = $(this);
		$.post('/ajax', { 'item' : lcom.data('id'), 'action' : 'delete_item' }, function(response){ 
			if(response.status == 1){
				lcom.hide().remove();
			}
		},'json');
	});

	$('.list-component').on('doubleTap', function(e){
		if ($(this).hasClass('not-finished')){
			var lcom = $(this);
			$.post('/ajax', { 'item' : lcom.data('id'), 'action' : 'finish_item' }, function(response){ 
				if(response.status == 1){
					lcom.removeClass('not-finished').addClass('finished');
				}
			},'json');
			
		} else if ($(this).hasClass('finished')){

		}
	});

	$('#done').on('click', function(){
		$('input').hide();
		$(this).hide();
		window.history.pushState({}, "View list", "/view/" + $('#list_id').val());
	});
});