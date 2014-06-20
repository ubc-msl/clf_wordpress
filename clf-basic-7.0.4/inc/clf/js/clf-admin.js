jQuery(document).ready(function() {
	
	var clf_update_checkbox = true;
	
    jQuery("#clf-unit-colour").wpColorPicker({
    		palettes: ['#5F62A9', '#98D5CA', '#D6DF30', '#FFDD00', '#E11553', '#EE523C'],
            change: function (event, ui) {
            	// console.log(clf_update_checkbox);
            	// we need to select the radio button
            	if( clf_update_checkbox) {
            			jQuery("#clf-default-colour").prop('checked', true );
            		}
            	clf_update_checkbox = true;
            	
            	// console.log(clf_update_checkbox);
            }
        });
	
    
    // Update colour field when default UBC Unit colour is picked
    jQuery(".ubc-default-colour").click(function() {
    	clf_update_colour();
    });
    
    // Display Faculty Dropdown
    jQuery("#faculty-ans input").click(function () {
    	if (jQuery(this).val() == "yes") {
    		jQuery("#faculty-name").slideDown();
    	} else {
    		jQuery("#faculty-name").slideUp();
    	}
    });

    jQuery("#faculty-address-ans input").click(function() {
      if (jQuery(this).val() == "yes") {
        jQuery("#faculty-address").slideDown();
      } else {
        jQuery("#faculty-address").slideUp();
      }
    });
    
    // Theme Colour and Unit Colour updater 
    jQuery("#clf-theme input").click(function () {
    	var selected = jQuery(this).val();
    	clf_update_selection( selected );
    	
    });
    
    jQuery("#clf-theme input:checked").each( function (){
    	var selected = jQuery(this).val();
    	clf_update_selection( selected );
    });
    
    
    function clf_update_selection( selected ){
    
    	if (selected == "bw" || selected == "wg") {
    		jQuery("#ubc-grey input").prop('disabled', true);
    		jQuery("#ubc-grey").addClass("transparent");
    		jQuery("#ubc-blue input").prop('disabled', false);
    		jQuery("#ubc-blue").removeClass("transparent");
    		
    		if (jQuery("#ubc-grey input").is(':checked')) {
    			jQuery("#ubc-blue input").attr('checked', true);
    			clf_update_colour();	
    		}
    		
    	} else {
    		jQuery("#ubc-blue input").prop('disabled', true);
    		jQuery("#ubc-blue").addClass("transparent");
    		jQuery("#ubc-grey input").prop('disabled', false);
    		jQuery("#ubc-grey").removeClass("transparent");
    		
    		if (jQuery("#ubc-blue input").is(':checked')) {
    			jQuery("#ubc-grey input").attr('checked', true);
    			clf_update_colour();	
    		}
    	}
    }
    
    function clf_update_colour () {
    	if ( jQuery("#ubc-grey input").is(':checked') ) {
    		clf_update_checkbox = false;
    		jQuery("#clf-unit-colour").wpColorPicker( 'color','#2F5D7C');
    	}
    	else if (jQuery("#ubc-blue input").is(':checked')) {
    		clf_update_checkbox = false;
    		jQuery("#clf-unit-colour").wpColorPicker('color','#002145');
    	}
    }
    
    // Select Default Colour
    if (jQuery("#clf-unit-colour").val() == "#002145") {
    	jQuery("#ubc-blue input").attr('checked', true);
    	jQuery("#ubc-grey input").prop('disabled', true);
    	jQuery("#ubc-grey").addClass("transparent");
   	} else if (jQuery("#clf-unit-colour").val() == "#2F5D7C") {
   		jQuery("#ubc-grey input").attr('checked', true);
   		jQuery("#ubc-blue input").prop('disabled', true);
   		jQuery("#ubc-blue").addClass("transparent");
   	} 
   	
   	if (jQuery("#faculty-ans input:checked").val() == "yes") {
   		jQuery("#faculty-name").slideDown();	
   	}
   	
    if (jQuery("#faculty-address-ans input:checked").val() == "yes") {
      jQuery("#faculty-address").slideDown();
    }
    
});