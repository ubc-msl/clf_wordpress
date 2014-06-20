jQuery( function($) {
	
	var navigation_options = {
		
		previous 	: {},
		next		: {}, 
		shell 		: {},
		init: function() {
		
			navigation_options.previous = $('#previous-post-text');
			navigation_options.next     = $('#next-post-text')
			navigation_options.shell 	= $('#previous-next-options');
		
			// make sure that the js selects
			navigation_options.shell.find(' input').click( function(){
				
				var el_value = $(this).val();
				
				switch(el_value) {
					
					case 'default':
						navigation_options.previous.val('Previous');
						navigation_options.next.val('Next');
					break;
					
					case 'post-title':
						navigation_options.previous.val('%title');
						navigation_options.next.val('%title');
					break;
				}
				
			});
			
			navigation_options.previous.keypress(function(){
				navigation_options.chenge_fields_to_custom();
			});
			
			navigation_options.next.keypress(function(){
				navigation_options.chenge_fields_to_custom();
			});
		},
		chenge_fields_to_custom : function (shell){
		
			navigation_options.shell.find('input[value$="custom"]').prop('checked',true);
		}
	
	}
	
	navigation_options.init();

});