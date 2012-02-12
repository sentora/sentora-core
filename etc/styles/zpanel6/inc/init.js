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
    $("#zmodule_account").show();
    $("#zmodule_account_a").click(function(){
        if ($("#zmodule_account").is(":visible")) {
			$("#zmodule_account").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_account-state', 'hiding');
            return false;
        } else {
            $("#zmodule_account").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_account-state', 'showing');
            return false;
        }
    }); 
    var zmoduleaccountstate = $.cookie('zmodule_account-state'); 
    if (zmoduleaccountstate == 'hiding') {
            $("#zmodule_account").hide();
            $("#zmodule_account_a").addClass("active");
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
    $("#zmodule_admin").show();
    $("#zmodule_admin_a").click(function(){
        if ($("#zmodule_admin").is(":visible")) {
			$("#zmodule_admin").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_admin-state', 'hiding');
            return false;
        } else {
            $("#zmodule_admin").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_admin-state', 'showing');
            return false;
        }
    }); 
    var zmoduleadminstate = $.cookie('zmodule_admin-state'); 
    if (zmoduleadminstate == 'hiding') {
            $("#zmodule_admin").hide();
            $("#zmodule_admin_a").addClass("active");
    };
})
//Database DIV
$(document).ready(function() { 
    $("#zmodule_databases").show();
    $("#zmodule_databases_a").click(function(){
        if ($("#zmodule_databases").is(":visible")) {
			$("#zmodule_databases").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_databases-state', 'hiding');
            return false;
        } else {
            $("#zmodule_databases").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_databases-state', 'showing');
            return false;
        }
    }); 
    var zmoduledatabasesstate = $.cookie('zmodule_databases-state'); 
    if (zmoduledatabasesstate == 'hiding') {
            $("#zmodule_databases").hide();
            $("#zmodule_databases_a").addClass("active");
    };
})
//Domains DIV
$(document).ready(function() { 
    $("#zmodule_domains").show();
    $("#zmodule_domains_a").click(function(){
        if ($("#zmodule_domains").is(":visible")) {
			$("#zmodule_domains").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_domains-state', 'hiding');
            return false;
        } else {
            $("#zmodule_domains").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_domains-state', 'showing');
            return false;
        }
    }); 
    var zmoduledomainsstate = $.cookie('zmodule_domains-state'); 
    if (zmoduledomainsstate == 'hiding') {
            $("#zmodule_domains").hide();
            $("#zmodule_domains_a").addClass("active");
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
    $("#zmodule_storage").show();
    $("#zmodule_storage_a").click(function(){
        if ($("#zmodule_storage").is(":visible")) {
			$("#zmodule_storage").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zmodule_storage-state', 'hiding');
            return false;
        } else {
            $("#zmodule_storage").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zmodule_storage-state', 'showing');
            return false;
        }
    }); 
    var zmodulestoragestate = $.cookie('zmodule_storage-state'); 
    if (zmodulestoragestate == 'hiding') {
            $("#zmodule_storage").hide();
            $("#zmodule_storage_a").addClass("active");
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