/*
 * @copyright 2014-2015 Sentora Project (http://www.sentora.org/) 
 * Sentora is a GPL fork of the ZPanel Project whose original header follows:
 *
 * dns.js
 *
 * @package ZPanel DNS Manager
 * @version 1.0.0
 * @author Jason Davis - <jason.davis.fl@gmail.com>
 * @copyright (c) 2013 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 */

/*
NEW DNS Module JavaScript by Jason Davis
7/6/2013
 */


var SentoraDNS = {

    unsavedChanges: false,

    init: function() {

        //this.cache.dnsTitleId = $("#dnsTitle");

        // Cache some Selectors for increased performance
        SentoraDNS.cache.dnsTitleId = $("#dnsTitle");

        SentoraDNS.events.init();

    },

    cache: {},

    events: {

        promptBeforeClose: function(e) {
            var e = e || window.event;
            if (!SentoraDNS.unsavedChanges) return;

            if (e) {
                e.returnValue = 'There are unsaved changes.  Are you sure you wish to leave without saving these changes?';
            }
            return 'There are unsaved changes.  Are you sure you wish to leave without saving these changes?';
        },

        init: function() {

            var mXWarning,
            nSWarning;

            // If user trys to leave the page with UN-SAVED changes, we will Alert them
            $(window).on('beforeunload', SentoraDNS.events.promptBeforeClose);


            //$("#typeMX div.hostName input").on('keypress',function() {
            $(document).on('keypress', '#typeMX div.hostName > input', function() {
                Sentora.utils.log('MX hostname change');
                var hostnameSelector = $(this);
                mXWarning = 'The host name portion of an MX record is typically left blank.<BR/>' +
                    'Only enter host name if you want the email address to be similar to <strong>username@hostname.example.com</strong>, ' +
                    'where hostname is what you are entering and example.com is the current domain name.';
                Sentora.dialog.confirm({
                    title: 'WARNING',
                    message: mXWarning,
                    width: 300,
                    cancelCallback: function(hostname) {
                        hostnameSelector.val('');
                    },
                    cancelButton: {
                        text: 'Cancel',
                        show: true,
                        class: 'btn-default'
                    },
                    okButton: {
                        text: 'Confirm',
                        show: true,
                        class: 'btn-primary'
                    },
                });

                $(document).off('keypress', '#typeMX div.hostName > input');
                //$("div.dnsRecordMX div.hostName input").die();
            });


            // Show Dialog when Delete button hit



            // Show Dialog if Hostname Matches the Domain Name
            $(document).on('change','div.hostName > input',function() {
                Sentora.utils.log('hostname change fired');
                var hostnameSelector = $(this);
                var hostName = $(this).val();
                var domainName = $("#domainName").val();
                var pattern = new RegExp(domainName + "$","g");

                if ( hostName.match(pattern) != null ) {
                    var msg = '<strong>Warnig:</strong> A host name record has been entered with the domain name.<BR/><BR/>' +
                         'The result will be the following:<BR/><strong>' + $(this).val() + '.' + $("#domainName").val() + '</strong><BR/><BR/>' +
                         'If this is not what you intended, <strong>Click Cancel</strong> to remove the domain name from the host name field and enter in only the host value.';
                    Sentora.dialog.confirm({
                        title: 'WARNING',
                        message: msg,
                        width: 300,
                        cancelCallback: function (hostname) {
                            hostnameSelector.val('');
                        },
                        cancelButton: {text: 'Cancel', show: true, class: 'btn-default'},
                        okButton: {text: 'Confirm', show: true, class: 'btn-primary'},
                    });
                }
            });


            // Activate SAVE and UNDO Buttons when Record Row EDITED
            $(document).on("keydown", "#dnsRecords input", function() {
                //$("#dnsTitle").find(".save, .undo").removeClass("disabled");
                Sentora.utils.log(SentoraDNS.cache.dnsTitleId);
                Sentora.utils.log($("#dnsTitle"));
                //$("#dnsTitle").find(".save, .undo").removeClass("disabled");
                SentoraDNS.cache.dnsTitleId.find(".save, .undo").removeClass("disabled");
                $(".tab-pane > .add").find(".save").removeClass("disabled");
            });

            // Activate SAVE and UNDO Buttons when Record Row DELETED
            // $("#dnsRecords span.delete").on("click",function() {
            //     $("#dnsTitle a.save, #dnsTitle a.undo").removeClass("disabled");
            // });

            // Add new Record Row
            $("#dnsRecords div.add > .btn").click(function(e) {
                Sentora.utils.log('add record button clicked');
                SentoraDNS.records.addRow($(this));
                e.preventDefault();
            });

            // Mark Record as "Deleted" and change the view of it's Row to reflect a Deleted item
            $(document).on("click", ".delete", function(e) {
                SentoraDNS.records.deleteRow($(this));
                e.preventDefault();
            });

            // Show Undo button when editing an EXISTING ROW
            $(document).on("keydown", "div.dnsRecord input[type='text']", function() {
                $(this).parents("div.dnsRecord").find("button.undo").fadeIn('slow');
                SentoraDNS.unsavedChanges = true;
            });

            // Undo editing of an EXISTING ROW
            $("button.undo").on("click", function() {
                SentoraDNS.records.undoRow($(this));
            });

            //Save Changes
            //$("#dnsTitle").find(".save").removeClass("disabled");
            $("#dnsTitle a.save").click(function() {
                if ($(this).hasClass("disabled")) return false;
                Sentora.loader.showLoader();
                $("form").submit();
                return false;
            });

            $(".tab-pane > .add").find(".save").click(function() {
                if ($(this).hasClass("disabled")) return false;
                Sentora.loader.showLoader();
                $("form").submit();
                return false;
            });

            //Undo ALL Record Type Changes
            $("#dnsTitle a.undo").click(function() {
                if ($(this).hasClass("disabled")) return false;
                $("button.undo").click();
                $(".tab-pane .new").remove();
                $("#dnsTitle a.save, #dnsTitle a.undo").addClass("disabled");
                SentoraDNS.unsavedChanges = false;
                return false;
            });

            $("form").submit(function() {
                SentoraDNS.unsavedChanges = false;
                //Remove any entries that have no value for any relevant fields
                $("div.dnsRecord").each(function() {
                    var hasValue = false;
                    $("input", $(this)).not("input[name*='ttl'], input[name*='type']").filter(function() {
                        var val = $(this).val();
                        return val != "" || val > 0;
                    }).each(function() {
                        hasValue = true;
                    });
                    if (!hasValue) {
                        $(this).remove();
                    }
                });
                return true;
            });

        },

    },

    records: {

        // Add new record row
        addRow: function(record) {
            // Get correct DNS Record Template Div and Clone a copy of it
            var newRecord = record.parents("div.add").nextAll(".newRecord").clone();

            // Update New Record Counter Input field
            var counterElement = $("#dnsRecords input[name='newRecords']");
            var newId = parseInt(counterElement.attr("value"));
            newId++;
            counterElement.attr("value", newId);

            // Remove Labels from New records
            if (record.parents("div.add").siblings().length > 2) {
                Sentora.utils.log('div .add sibblings...');
                Sentora.utils.log(record.parents("div.add").siblings());
                //newRecord.find("label").remove();
            }

            // Set new record name
            $("input", newRecord).each(function() {
                var fieldName = $(this).attr("name").replace("proto_", "");
                $(this).attr("name", fieldName + "[new_" + newId + "]");
            });

            // Set CSS Class to mark record as NEW
            newRecord.addClass("dnsRecord new").removeClass("newRecord");
            newRecord.insertBefore(record.parents("div.add")).fadeIn();
            record.parents("div.records").scrollTop(record.parents("div.records").scrollTop() + 1000);
            $("#dnsTitle a.undo").removeClass("disabled");

        },


        save: function() {

        },

        undoRow: function(row) {

            // Remove .tabError from all Tabs that have child Divs with .dnsRecordError
            $(".records div.dnsRecordError").parents("div.records").each(function(index) {
                var id = this.id;
                $("a[href='#" + id + "']").removeClass("tabError");
            });

            // Remove Disabled class from text inputs
            //row.siblings().children('.input-small').addClass("disabled");

            row.parents("div.dnsRecord").find("input[type='text']").each(function() {
                var myName = $(this).attr("name");
                myName = "original_" + myName;

                $(this).val($("input[name='" + myName + "']").val());
                $(this).parents("div.dnsRecord").removeClass("dnsRecordError");
                $(this).parents("div.dnsRecord").find("div.errorMessage").remove();

                $(this).parents("div.dnsRecord").removeClass("deleted").find("input.delete").val("false");
            });
            row.fadeOut('fast');
        },


        deleteRow: function(row) {

            row.parents("div.dnsRecord").addClass("deleted").find("input.delete").val("true");
            row.parents("div.dnsRecord").find("button.undo").fadeIn('slow');
            // Add Disabled class to Deleted inputs
            //row.siblings().children('.input-small').addClass("disabled");
            $("#dnsTitle a.save, #dnsTitle a.undo").removeClass("disabled");
            SentoraDNS.unsavedChanges = true;

        },


        delete: function() {
            var target = document.getElementById('zloader_content');
        },
    }

};

$(function() {
    SentoraDNS.init();
});