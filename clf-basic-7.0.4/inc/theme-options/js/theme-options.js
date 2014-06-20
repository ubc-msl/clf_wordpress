jQuery( function($) {
	
	// trun sections into tabs
	$("#theme-options-shell").tabs();
	
	// import settings 
	$("#import-url-action").click( UBC_Collab_Theme_Options.import );
	// allows for the selection of the export url 
	$("#export-select-all").click( UBC_Collab_Theme_Options.select_export );
	// save the options via ajax
	$("#ubc-collab-theme-options-form").submit( UBC_Collab_Theme_Options.save_options )
	.on('change','.on-change',UBC_Collab_Theme_Options.form_changed)
	.on('keypress','.on-keypress',UBC_Collab_Theme_Options.form_changed);
	// scroll to the top if you click on the 
	$( "#submit-buttom" ).click( UBC_Collab_Theme_Options.scroll_up );
	
	$( '.close-update' ).live( 'click', UBC_Collab_Theme_Options.close_update );
	
	$( window ).bind( 'form_changed', UBC_Collab_Theme_Options.form_changed_action );
	
	$('.colour-picker').wpColorPicker({
            change: function (event, ui) {
            	
            	// link_color.wpColorPicker('color')
                //UBC_Collab_Theme_Options.pick_colour( el,  );
            },
            clear: function () {
            	
                //UBC_Collab_Theme_Options.pick_colour( el,  );
            },
            palettes: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']
        });
	
	
	jQuery(".explanation-help").click(function(event) {
		event.preventDefault();
		$(this).parent().find('div').slideToggle();
	});
	
});

var UBC_Collab_Theme_Options = {
	
	import: function( event ){
	
		event.preventDefault();
		
		var export_url = escape( jQuery("#import-url").val());
		
		if( export_url.replace(/\s/g,"") == "" ) {
			
			alert( 'Please enter some charachers' );
			return;
		}
		
		var valid = export_url.split('%3Faction%3D');
			
		if( 'ubc-collab-export' != valid[1] ) {
			
			alert('Make sure that settings are from a UBC Collab theme');
			return;
		}
		
		if( confirm("You are about to overwrite your theme settings! \n\nDo you want to continue? ") ) {
			
			// naive check if we are importing ubc collab options
			var data = {
				nonce: UBC_COLLAB.nonce_import,
				action: 'ubc-collab-import-options',
				url: export_url
			};
			
			jQuery('#ajax-loading-import').show();
			
			jQuery.post( ajaxurl, data, function( response ) {
				
				jQuery('#ajax-loading-import').hide();
				if( response.success ) {
					// location.reload( true ); 
					window.location = "#settings-imported";
				} else {
					jQuery("#import-response").html("Error: "+response.error );
				}
			}, "json" ); // end of post
			
			
		}
	},
	// select the export text area
	select_export: function( event ) {
		event.preventDefault();
		jQuery( "#export-url" ).select();
		
	},
	// save the options 
	save_options: function( event ) {
		event.preventDefault();
		jQuery('.ubc-collab-ajax-save-options').show();
		var data = jQuery(this).serializeArray();
		// console.log(data);
		
		data.push( { name: 'nonce', value: UBC_COLLAB.nonce_save } );
		data.push( { name: 'action', value: 'ubc-collab-save-options' } );
		
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery('.ubc-collab-ajax-save-options').hide();
			if( response.success ){
				// jQuery("#clf-base-ajax-feedback").hide();
				jQuery("#setting-error-settings_updated").fadeIn().html("<p>Success! The theme options have been updated. <a href='#' class='close-update'>close</a></p>").addClass('updated').removeClass('error');
				
				jQuery(".save-changes").hide();
				jQuery(window).unbind('beforeunload');
				
			} else { // an error occured 
				// jQuery("#clf-base-ajax-feedback").hide();
				jQuery("#setting-error-settings_updated").fadeIn().html("<p>Error! "+response.error+" <a href='#' class='close-update'>close</a></p>").addClass('error').removeClass('updated');
			}
			
		}, "json"); // end of post
		
	},
	scroll_up : function( event ) {
		
		jQuery( 'html,body' ).animate( { scrollTop: 0 }, "slow" );
	
	},
	// 
	close_update : function(){
		jQuery( "#setting-error-settings_updated" ).fadeOut( 'slow' );
		return false;
	},
	// form change
	form_changed: function(e) {
		
		jQuery(window).triggerHandler('form_changed');
	},
	// 
	form_changed_action: function(e) {
		
  		jQuery(".save-changes").fadeIn();
  		
  		jQuery( this ).bind( 'beforeunload', function() { 
  			return "Some change never got to this form never got saved";
  		});
	},
	pick_colour: function(){
	
	}
	
}