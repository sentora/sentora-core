//input styles
//remove Chrome's input border styling while keeping the autocomplete functionality intact.
$(document).ready(function() {
    if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
        $(window).load(function(){
            $('input:-webkit-autofill').each(function(){
                var text = $(this).val();
                var name = $(this).attr('name');
                $(this).after(this.outerHTML).remove();
                $('input[name=' + name + ']').val(text);
            });
        });
    }
});	
		
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

    });
});
		
//Account DIV	
$(document).ready(function() { 
    $("#zcat_account_information").show();
    $("#zcat_account_information_a").click(function(){
        if ($("#zcat_account_information").is(":visible")) {
            $("#zcat_account_information").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_account_information-state', 'hiding');
            return false;
        } else {
            $("#zcat_account_information").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_account_information-state', 'showing');
            return false;
        }
    }); 
    var zcataccountstate = $.cookie('zcat_account_information-state'); 
    if (zcataccountstate == 'hiding') {
        $("#zcat_account_information").hide();
        $("#zcat_account_information_a").addClass("active");
    };
})
//Advanced DIV
$(document).ready(function() { 
    $("#zcat_advanced").show();
    $("#zcat_advanced_a").click(function(){
        if ($("#zcat_advanced").is(":visible")) {
            $("#zcat_advanced").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_advanced-state', 'hiding');
            return false;
        } else {
            $("#zcat_advanced").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_advanced-state', 'showing');
            return false;
        }
    }); 
    var zcatadvancedstate = $.cookie('zcat_advanced-state'); 
    if (zcatadvancedstate == 'hiding') {
        $("#zcat_advanced").hide();
        $("#zcat_advanced_a").addClass("active");
    };
})
//Admin DIV
$(document).ready(function() { 
    $("#zcat_server_admin").show();
    $("#zcat_server_admin_a").click(function(){
        if ($("#zcat_server_admin").is(":visible")) {
            $("#zcat_server_admin").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_server_admin-state', 'hiding');
            return false;
        } else {
            $("#zcat_server_admin").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_server_admin-state', 'showing');
            return false;
        }
    }); 
    var zcatadminstate = $.cookie('zcat_server_admin-state'); 
    if (zcatadminstate == 'hiding') {
        $("#zcat_server_admin").hide();
        $("#zcat_server_admin_a").addClass("active");
    };
})
//Database DIV
$(document).ready(function() { 
    $("#zcat_database_management").show();
    $("#zcat_database_management_a").click(function(){
        if ($("#zcat_database_management").is(":visible")) {
            $("#zcat_database_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_database_management-state', 'hiding');
            return false;
        } else {
            $("#zcat_database_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_database_management-state', 'showing');
            return false;
        }
    }); 
    var zcatdatabasesstate = $.cookie('zcat_database_management-state'); 
    if (zcatdatabasesstate == 'hiding') {
        $("#zcat_database_management").hide();
        $("#zcat_database_management_a").addClass("active");
    };
})
//Domains DIV
$(document).ready(function() { 
    $("#zcat_domain_management").show();
    $("#zcat_domain_management_a").click(function(){
        if ($("#zcat_domain_management").is(":visible")) {
            $("#zcat_domain_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_domain_management-state', 'hiding');
            return false;
        } else {
            $("#zcat_domain_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_domain_management-state', 'showing');
            return false;
        }
    }); 
    var zcatdomainsstate = $.cookie('zcat_domain_management-state'); 
    if (zcatdomainsstate == 'hiding') {
        $("#zcat_domain_management").hide();
        $("#zcat_domain_management_a").addClass("active");
    };
})
//Mail DIV
$(document).ready(function() { 
    $("#zcat_mail").show();
    $("#zcat_mail_a").click(function(){
        if ($("#zcat_mail").is(":visible")) {
            $("#zcat_mail").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_mail-state', 'hiding');
            return false;
        } else {
            $("#zcat_mail").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_mail-state', 'showing');
            return false;
        }
    }); 
    var zcatmailstate = $.cookie('zcat_mail-state'); 
    if (zcatmailstate == 'hiding') {
        $("#zcat_mail").hide();
        $("#zcat_mail_a").addClass("active");
    };
})
//Reseller DIV
$(document).ready(function() { 
    $("#zcat_reseller").show();
    $("#zcat_reseller_a").click(function(){
        if ($("#zcat_reseller").is(":visible")) {
            $("#zcat_reseller").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_reseller-state', 'hiding');
            return false;
        } else {
            $("#zcat_reseller").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_reseller-state', 'showing');
            return false;
        }
    }); 
    var zcatresellerstate = $.cookie('zcat_reseller-state'); 
    if (zcatresellerstate == 'hiding') {
        $("#zcat_reseller").hide();
        $("#zcat_reseller_a").addClass("active");
    };
})
//Storage DIV
$(document).ready(function() { 
    $("#zcat_file_management").show();
    $("#zcat_file_management_a").click(function(){
        if ($("#zcat_file_management").is(":visible")) {
            $("#zcat_file_management").slideUp("fast");
            $(this).addClass("active");
            $.cookie('zcat_file_management-state', 'hiding');
            return false;
        } else {
            $("#zcat_file_management").slideDown("fast");
            $(this).removeClass("active");
            $.cookie('zcat_file_management-state', 'showing');
            return false;
        }
    }); 
    var zcatstoragestate = $.cookie('zcat_file_management-state'); 
    if (zcatstoragestate == 'hiding') {
        $("#zcat_file_management").hide();
        $("#zcat_file_management_a").addClass("active");
    };
})
//Statsdata Arrows DIV
$(document).ready(function() { 
    $("#statsdata_wrapper").show();
    $("#arrow_small_left").hide();
    $("#arrow_left").hide();
    $("#statsdata_wrapper_a_small_right").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_small_left").show();
            $("#arrow_small_right").hide();
            $("#arrow_left").show();
            $("#arrow_right").hide();
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_small_left").hide();
            $("#arrow_small_right").show();
            $("#arrow_left").hide();
            $("#arrow_right").show();
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    });
    $("#statsdata_wrapper_a_small_left").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_small_left").show();
            $("#arrow_small_right").hide();
            $("#arrow_left").show();
            $("#arrow_right").hide();
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_small_left").hide();
            $("#arrow_small_right").show();
            $("#arrow_left").hide();
            $("#arrow_right").show();
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    });
    $("#statsdata_wrapper_a_right").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_left").show();
            $("#arrow_right").hide();
            $("#arrow_small_left").show();
            $("#arrow_small_right").hide();
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_left").hide();
            $("#arrow_right").show();
            $("#arrow_small_left").hide();
            $("#arrow_small_right").show();
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    });
    $("#statsdata_wrapper_a_left").click(function(){
        if ($("#statsdata_wrapper").is(":visible")) {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_left").show();
            $("#arrow_right").hide();
            $("#arrow_small_left").show();
            $("#arrow_small_right").hide();
            $(this).addClass("active");
            $.cookie('statsdata_wrapper-state', 'hiding');
            return false;
        } else {
            $("#statsdata_wrapper").animate({
                width:'toggle'
            },350);
            $("#arrow_left").hide();
            $("#arrow_right").show();
            $("#arrow_small_left").hide();
            $("#arrow_small_right").show();
            $(this).removeClass("active");
            $.cookie('statsdata_wrapper-state', 'showing');
            return false;
        }
    });
    var zcatstatsdatastate = $.cookie('statsdata_wrapper-state'); 
    if (zcatstatsdatastate == 'hiding') {
        $("#statsdata_wrapper").hide();
        $("#arrow_small_right").hide();
        $("#arrow_small_left").show();
        $("#arrow_right").hide();
        $("#arrow_left").show();
        $("#arrow_small_left").addClass("active");
        $("#arrow_left").addClass("active");
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
    var zcataccountinfostate = $.cookie('statsdata_accountinfo-state'); 
    if (zcataccountinfostate == 'hiding') {
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
    var zcatserverinfostate = $.cookie('statsdata_serverinfo-state'); 
    if (zcatserverinfostate == 'hiding') {
        $("#statsdata_serverinfo").hide();
        $("#statsdata_serverinfo_a").addClass("active");
    };
})
//Domaininfo DIV
$(document).ready(function() { 
    $("#statsdata_domaininfo").show();
    $("#statsdata_domaininfo_a").click(function(){
        if ($("#statsdata_domaininfo").is(":visible")) {
            $("#statsdata_domaininfo").slideUp("slow");
            $(this).addClass("active");
            $.cookie('statsdata_domaininfo-state', 'hiding');
            return false;
        } else {
            $("#statsdata_domaininfo").slideDown("slow");
            $(this).removeClass("active");
            $.cookie('statsdata_domaininfo-state', 'showing');
            return false;
        }
    }); 
    var zcatserverinfostate = $.cookie('statsdata_domaininfo-state'); 
    if (zcatserverinfostate == 'hiding') {
        $("#statsdata_domaininfo").hide();
        $("#statsdata_domaininfo_a").addClass("active");
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
    var zcatheaderbottomstate = $.cookie('header_bottom-state'); 
    if (zcatheaderbottomstate == 'hiding') {
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
            my: 'middle left', 
            at: 'top right'
        }
    });
});
//Zannounce
$(document).ready(function() {
    $("#zannounce").hide();
    //$("#zannounce").fadeIn();
    $("#zannounce").slideDown("slow").animate({
        opacity: 1.0
    },6000).fadeOut();
    $('#zannounce_a').click(function() {
        $("#zannounce_a").fadeOut();
        $("#zannounce").hide();
    });
//$("#zannounce").fadeIn().animate({ opacity: 1.0 },3000).fadeOut();
//$("#zannounce").slideDown("slow").animate({ opacity: 1.0 },3000).fadeOut();
//$("#zannounce").slideDown("slow");
//$('#zannounce').hide().fadeIn('3000');
});

//show the page after all jquery is loaded
//$(document).ready(function() { 
//   $("body").show(); 
//});

//Bind zloader to button click	
$(document).ready(function(){
    $('#button').click(function() {
        $('#zloader_overlay').fadeIn('fast', function() {
            $("#zloader").show();
            showDiv();
        });
    });
});
