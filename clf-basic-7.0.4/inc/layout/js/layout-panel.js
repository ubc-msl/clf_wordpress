jQuery( function($) {
	var slider_div = $("#content-width-range");
	var layout_width_div = $('#layout-width');
	// make sure that the js selects
	$('#layout-options .option').click( function(){
		var radio = $(this).find('input');
		
		
		radio.prop("checked", true).trigger('change');
		
		
		layout_width_div[0].className = radio[0].value;
		
		if( radio[0].value == 'l1-column' || radio[0].value == 'default' ) {
			 layout_width_div.slideUp();
		} else {
			 layout_width_div.slideDown();
		}
		
		
		var content = $(this).find('.content-shell');
		
		var html = content.html();
		var classes = content.attr('class');
		html = '<div class="'+classes+'">'+html+'</div>';
		$("#ubc-collab-main-container").html(html);
		
		
		create_slider( radio[0].value , slider_div );
		
	});
	// header widget slider
	$('#ubc-collab-theme-options-header-widget').change( function(){
		
		if( $(this).prop("checked") ){
			$("#header-widget-area").slideDown();
		} else {
			$("#header-widget-area").slideUp();
		}
	});
	
	
	create_slider( layout_width_div[0].className , slider_div  );
	
	
	/**
	 * create_slider function.
	 * 
	 * @access public
	 * @param mixed slider_type
	 * @param mixed elm
	 * @return void
	 */
	function create_slider( slider_type, elm ) {
		
		// lets destroy it first
		elm.slider();
		elm.slider('destroy');
		var total = 0;
		var range_values = [];
		jQuery('#ubc-collab-main-container').find('input').each( function( index, input_elm ) {
			total += parseInt(input_elm.value)
			range_values.push( parseInt(total) );
			
		});
		var last = range_values.pop();
		var range_type = true;
		
		switch( slider_type ) {
			
			case 'l1-column':
			break;
			
			case 'l2-column-ms':
				range_type = 'max';
				//var range_values = [9];
			break;
			
			case 'l2-column-sm':
				range_type = 'max';
				//var range_values = [3];
			break;
			
			case 'l3-column-msp':
				
				//var range_values = [ 6, 9 ];
			break;
			
			case 'l3-column-pms':
				//var range_values = [ 3, 9 ];
				
			break;
			
			case 'l3-column-psm':
				//var range_values = [ 3, 6 ];
				
			break;
		
		} // end of switch
		
		elm.slider({
			range: range_type,
			min: 0,
			step:1,
			max: 12,
			values: range_values,
			slide: function( event, ui ) {
				
				var total_span = 12;
				var previous_span = 0;
				$('#ubc-collab-main-container').find('.content-shell').children('div').each(function(index, div_wrap){
					
					var size;
					
					var inputs = $('#ubc-collab-main-container').find('input');
					if( typeof ui.values[index] !== 'undefined') {
						total_span =  12 - ui.values[index];
						
						
						if( previous_span == 0) {
							size = ui.values[index]
						} else {
							size = ( ui.values[index] - previous_span );
						}
						previous_span = ui.values[index];
					} else {
						size = total_span;
					}
					
					div_wrap.className = 'span'+size;
					inputs[index].value = size;
					inputs.eq(index).trigger('change');
					
					
				});	
				
										
			}
		});
	}
	
});
