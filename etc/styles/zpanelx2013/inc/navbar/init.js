//JQUERY Nav Bar

/*
*	Below options are dynamically generated with navbar class
*	however, you may add or change options here.
*	
*	content: $('name_of_module_catagory').next().html(),";
*	showSpeed: 400
*/

$(function(){
    // NAVBUTTONS
    $('.fg-navbutton').hover(
        function(){
            $(this).removeClass('ui-state-default').addClass('ui-state-focus');
        },
        function(){
            $(this).removeClass('ui-state-focus').addClass('ui-state-default');
        }
        );
    // BUTTONS
    $('.fg-button').hover(
        function(){
            $(this).removeClass('ui-state-default').addClass('ui-state-focus');
        },
        function(){
            $(this).removeClass('ui-state-focus').addClass('ui-state-default');
        }
        );
    // MENUS    	
    $('#account_information').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
	
    $('#account_information').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
	
    $('#server_admin').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
		
    $('#advanced').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
		
    $('#database_management').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
		
    $('#domain_management').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
		
    $('#mail').menu({ 
        //ADD ADDITIONAL JQUERY HERE
        });
	
/* EXAMPLES
	$('#hierarchy').menu({
		content: $('#hierarchy').next().html(),
		crumbDefaultText: ' '
	});
	$('#hierarchybreadcrumb').menu({
		content: $('#hierarchybreadcrumb').next().html(),
		backLink: false
	});
	// or from an external source
	$.get('menuContent.html', function(data){ // grab content from another page
		$('#flyout').menu({ content: data, flyOut: true });
	});
	*/
});