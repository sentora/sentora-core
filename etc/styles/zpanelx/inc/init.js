		//input styles
		$(document).ready(function() {
			$('input[type="text"]').addClass("idleField");
       		$('input[type="text"]').focus(function() {
       			$(this).removeClass("idleField").addClass("focusField");
    		    
				if(this.value != this.defaultValue){
	    			this.select();
	    		}
    		});
    		$('input[type="text"]').blur(function() {
    			$(this).removeClass("focusField").addClass("idleField");
    		    if ($.trim(this.value) == ''){
			    	this.value = (this.defaultValue ? this.defaultValue : '');
				}
    		});
		});			

		$(document).ready(function() {
			$('input[type="text"],textarea').addClass("idleField");
       		$('input[type="text"],textarea').focus(function() {
       			$(this).removeClass("idleField").addClass("focusField");
    		    
				if(this.value != this.defaultValue){
	    			this.select();
	    		}
    		});
    		$('input[type="text"],textarea').blur(function() {
    			$(this).removeClass("focusField").addClass("idleField");
    		    if ($.trim(this.value) == ''){
			    	this.value = (this.defaultValue ? this.defaultValue : '');
				}
    		});
		});			

		$(document).ready(function() {
			$('input[type="text"],select').addClass("idleField");
       		$('input[type="text"],textarea').focus(function() {
       			$(this).removeClass("idleField").addClass("focusField");
    		    
				if(this.value != this.defaultValue){
	    			this.select();
	    		}
    		});
    		$('input[type="text"],textarea').blur(function() {
    			$(this).removeClass("focusField").addClass("idleField");
    		    if ($.trim(this.value) == ''){
			    	this.value = (this.defaultValue ? this.defaultValue : '');
				}
    		});
		});			

		$(document).ready(function() {
			$('input[type="password"]').addClass("idleField");
       		$('input[type="password"]').focus(function() {
       			$(this).removeClass("idleField").addClass("focusField");
    		    if (this.value == this.defaultValue){ 
    		    	this.value = '';
				}
				if(this.value != this.defaultValue){
	    			this.select();
	    		}
    		});
    		$('input[type="password"]').blur(function() {
    			$(this).removeClass("focusField").addClass("idleField");
    		    if ($.trim(this.value) == ''){
			    	this.value = (this.defaultValue ? this.defaultValue : '');
				}
    		});
		});
		
		//submit button
		$(document).ready(function(){
  			$('.mainlayout').jqf1();
		});
		
		
	
//Account DIV	
$(document).ready(function() { 
    $("#zmodule_account_information").show();
    $("#zmodule_account_information_a").click(function(){
        if ($("#zmodule_account_information").is(":visible")) {
			$("#zmodule_account_information").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_account_information-state', 'hiding');
            return false;
        } else {
            $("#zmodule_account_information").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_account_information-state', 'showing');
            return false;
        }
    }); 
    var zmoduleaccountstate = $.cookie('zmodule_account_information-state'); 
    if (zmoduleaccountstate == 'hiding') {
            $("#zmodule_account_information").hide();
            $("#zmodule_account_information_a").addClass("active");
    };
})
//Advanced DIV
$(document).ready(function() { 
    $("#zmodule_advanced").show();
    $("#zmodule_advanced_a").click(function(){
        if ($("#zmodule_advanced").is(":visible")) {
			$("#zmodule_advanced").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_advanced-state', 'hiding');
            return false;
        } else {
            $("#zmodule_advanced").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_advanced-state', 'showing');
            return false;
        }
    }); 
    var zmoduleadvancedstate = $.cookie('zmodule_advanced-state'); 
    if (zmoduleadvancedstate == 'hiding') {
            $("#zmodule_advanced").hide();
            $("#zmodule_advanced_a").addClass("active");
    };
})
//Admin DIV
$(document).ready(function() { 
    $("#zmodule_server_admin").show();
    $("#zmodule_server_admin_a").click(function(){
        if ($("#zmodule_server_admin").is(":visible")) {
			$("#zmodule_server_admin").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_server_admin-state', 'hiding');
            return false;
        } else {
            $("#zmodule_server_admin").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_server_admin-state', 'showing');
            return false;
        }
    }); 
    var zmoduleadminstate = $.cookie('zmodule_server_admin-state'); 
    if (zmoduleadminstate == 'hiding') {
            $("#zmodule_server_admin").hide();
            $("#zmodule_server_admin_a").addClass("active");
    };
})
//Database DIV
$(document).ready(function() { 
    $("#zmodule_database_management").show();
    $("#zmodule_database_management_a").click(function(){
        if ($("#zmodule_database_management").is(":visible")) {
			$("#zmodule_database_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_database_management-state', 'hiding');
            return false;
        } else {
            $("#zmodule_database_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_database_management-state', 'showing');
            return false;
        }
    }); 
    var zmoduledatabasesstate = $.cookie('zmodule_database_management-state'); 
    if (zmoduledatabasesstate == 'hiding') {
            $("#zmodule_database_management").hide();
            $("#zmodule_database_management_a").addClass("active");
    };
})
//Domains DIV
$(document).ready(function() { 
    $("#zmodule_domain_management").show();
    $("#zmodule_domain_management_a").click(function(){
        if ($("#zmodule_domain_management").is(":visible")) {
			$("#zmodule_domain_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_domain_management-state', 'hiding');
            return false;
        } else {
            $("#zmodule_domain_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_domain_management-state', 'showing');
            return false;
        }
    }); 
    var zmoduledomainsstate = $.cookie('zmodule_domain_management-state'); 
    if (zmoduledomainsstate == 'hiding') {
            $("#zmodule_domain_management").hide();
            $("#zmodule_domain_management_a").addClass("active");
    };
})
//Mail DIV
$(document).ready(function() { 
    $("#zmodule_mail").show();
    $("#zmodule_mail_a").click(function(){
        if ($("#zmodule_mail").is(":visible")) {
			$("#zmodule_mail").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_mail-state', 'hiding');
            return false;
        } else {
            $("#zmodule_mail").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_mail-state', 'showing');
            return false;
        }
    }); 
    var zmodulemailstate = $.cookie('zmodule_mail-state'); 
    if (zmodulemailstate == 'hiding') {
            $("#zmodule_mail").hide();
            $("#zmodule_mail_a").addClass("active");
    };
})
//Reseller DIV
$(document).ready(function() { 
    $("#zmodule_reseller").show();
    $("#zmodule_reseller_a").click(function(){
        if ($("#zmodule_reseller").is(":visible")) {
			$("#zmodule_reseller").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_reseller-state', 'hiding');
            return false;
        } else {
            $("#zmodule_reseller").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_reseller-state', 'showing');
            return false;
        }
    }); 
    var zmoduleresellerstate = $.cookie('zmodule_reseller-state'); 
    if (zmoduleresellerstate == 'hiding') {
            $("#zmodule_reseller").hide();
            $("#zmodule_reseller_a").addClass("active");
    };
})
//Storage DIV
$(document).ready(function() { 
    $("#zmodule_file_management").show();
    $("#zmodule_file_management_a").click(function(){
        if ($("#zmodule_file_management").is(":visible")) {
			$("#zmodule_file_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_file_management-state', 'hiding');
            return false;
        } else {
            $("#zmodule_file_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_file_management-state', 'showing');
            return false;
        }
    }); 
    var zmodulestoragestate = $.cookie('zmodule_file_management-state'); 
    if (zmodulestoragestate == 'hiding') {
            $("#zmodule_file_management").hide();
            $("#zmodule_file_management_a").addClass("active");
    };
})
//Statsdata DIV
$(document).ready(function() { 
    $("#statsdata_wrapper").show();
    $("#statsdata_wrapper_a").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
			$("#statsdata_wrapper").animate({width:'toggle'},350);
			$('#statsdata_wrapper_a').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
			$('#statsdata_wrapper_a_small').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({width:'toggle'},350);
			$('#statsdata_wrapper_a').css("background-image", "url(/etc/styles/zpanelx/images/arrow_right.png)");
			$('#statsdata_wrapper_a_small').css("background-image", "url(/etc/styles/zpanelx/images/arrow_right.png)");
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    }); 
    $("#statsdata_wrapper_a_small").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
			$("#statsdata_wrapper").animate({width:'toggle'},350);
			$('#statsdata_wrapper_a').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
			$('#statsdata_wrapper_a_small').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({width:'toggle'},350);
			$('#statsdata_wrapper_a').css("background-image", "url(/etc/styles/zpanelx/images/arrow_right.png)");
			$('#statsdata_wrapper_a_small').css("background-image", "url(/etc/styles/zpanelx/images/arrow_right.png)");
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    }); 
    var zmodulestatsdatastate = $.cookie('statsdata_wrapper-state'); 
    if (zmodulestatsdatastate == 'hiding') {
            $("#statsdata_wrapper").hide();
			$('#statsdata_wrapper_a').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
			$('#statsdata_wrapper_a_small').css("background-image", "url(/etc/styles/zpanelx/images/arrow_left.png)");
            $("#statsdata_wrapper_a").addClass("active");
    };
})
//Accountinfo DIV
$(document).ready(function() { 
    $("#statsdata_accountinfo").show();
    $("#statsdata_accountinfo_a").click(function(){
        if ($("#statsdata_accountinfo").is(":visible")) {
			$("#statsdata_accountinfo").slideUp("slow");
            $(this).addClass("active");
            $.cookie('statsdata_accountinfo-state', 'hiding');
            return false;
        } else {
            $("#statsdata_accountinfo").slideDown("slow");
            $(this).removeClass("active");
            $.cookie('statsdata_accountinfo-state', 'showing');
            return false;
        }
    }); 
    var zmoduleaccountinfostate = $.cookie('statsdata_accountinfo-state'); 
    if (zmoduleaccountinfostate == 'hiding') {
            $("#statsdata_accountinfo").hide();
            $("#statsdata_accountinfo_a").addClass("active");
    };
})
//Serverinfo DIV
$(document).ready(function() { 
    $("#statsdata_serverinfo").show();
    $("#statsdata_serverinfo_a").click(function(){
        if ($("#statsdata_serverinfo").is(":visible")) {
			$("#statsdata_serverinfo").slideUp("slow");
            $(this).addClass("active");
            $.cookie('statsdata_serverinfo-state', 'hiding');
            return false;
        } else {
            $("#statsdata_serverinfo").slideDown("slow");
            $(this).removeClass("active");
            $.cookie('statsdata_serverinfo-state', 'showing');
            return false;
        }
    }); 
    var zmoduleserverinfostate = $.cookie('statsdata_serverinfo-state'); 
    if (zmoduleserverinfostate == 'hiding') {
            $("#statsdata_serverinfo").hide();
            $("#statsdata_serverinfo_a").addClass("active");
    };
})
//Header DIV
$(document).ready(function() { 
    $("#header_bottom").show();
	$("#header_top").hide();
    $("#header_top_a").click(function(){
        if ($("#header_bottom").is(":visible")) {
			$("#header_bottom").slideUp("slow");
			$("#header_spacer").slideUp("slow");
			$("#header_top").slideDown("slow");
            $(this).addClass("active");
            $.cookie('header_bottom-state', 'hiding');
            return false;
        } else {
            $("#header_bottom").slideDown("slow");
            $("#header_spacer").slideDown("slow");
			$("#header_top").slideUp("slow");
            $(this).removeClass("active");
            $.cookie('header_bottom-state', 'showing');
            return false;
        }
    }); 
    $("#header_top_a_small").click(function(){
        if ($("#header_bottom").is(":visible")) {
			$("#header_bottom").slideUp("slow");
			$("#header_spacer").slideUp("slow");
			$("#header_top").slideDown("slow");
            $(this).addClass("active");
            $.cookie('header_bottom-state', 'hiding');
            return false;
        } else {
            $("#header_bottom").slideDown("slow");
            $("#header_spacer").slideDown("slow");
			$("#header_top").slideUp("slow");
            $(this).removeClass("active");
            $.cookie('header_bottom-state', 'showing');
            return false;
        }
    });
    var zmoduleheaderbottomstate = $.cookie('header_bottom-state'); 
    if (zmoduleheaderbottomstate == 'hiding') {
            $("#header_bottom").hide();
            $("#header_spacer").hide();
            $("#header_top").show();
            $("#header_top_a").addClass("active");
			$("#header_top_a_small").addClass("active");
    };
})
//Server Time
$(function($) {
   var options = {
        timeNotation: '12h',
        am_pm: true,
        fontFamily: 'Lucida Grande, Verdana, Arial, Sans-Serif',
        fontSize: '10px',
        foreground: '#333333',
        background: ''
      }; 
   $('.jclock').jclock(options);
});
//Tool Tips
$(document).ready(function() {
   $('#content a[href][title]').qtip({
      content: {
         text: false
      },
      position: {
      my: 'middle left', at: 'top right'
   }
   });
});
//show the page after all jquery is loaded
$(document).ready(function() { 
   $("body").show(); 
});