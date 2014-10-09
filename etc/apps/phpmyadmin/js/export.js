/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Functions used in the export tab
 *
 */

/**
 * Unbind all event handlers before tearing down a page
 */
AJAX.registerTeardown('export.js', function() {
    $("#plugins").unbind('change');
    $("input[type='radio'][name='sql_structure_or_data']").unbind('change');
    $("input[type='radio'][name='latex_structure_or_data']").unbind('change');
    $("input[type='radio'][name='odt_structure_or_data']").unbind('change');
    $("input[type='radio'][name='texytext_structure_or_data']").unbind('change');
    $("input[type='radio'][name='htmlword_structure_or_data']").unbind('change');
    $("input[type='radio'][name='sql_structure_or_data']").unbind('change');
    $("input[type='radio'][name='output_format']").unbind('change');
    $("#checkbox_sql_include_comments").unbind('change');
    $("#plugins").unbind('change');
    $("input[type='radio'][name='quick_or_custom']").unbind('change');
    $("input[type='radio'][name='allrows']").unbind('change');
});

AJAX.registerOnload('export.js', function () {
    /**
     * Toggles the hiding and showing of each plugin's options
     * according to the currently selected plugin from the dropdown list
     */
    $("#plugins").change(function() {
        $("#format_specific_opts div.format_specific_options").hide();
        var selected_plugin_name = $("#plugins option:selected").val();
        $("#" + selected_plugin_name + "_options").show();
     });

    /**
     * Toggles the enabling and disabling of the SQL plugin's comment options that apply only when exporting structure
     */
    $("input[type='radio'][name='sql_structure_or_data']").change(function() {
        var comments_are_present = $("#checkbox_sql_include_comments").prop("checked");
        var show = $("input[type='radio'][name='sql_structure_or_data']:checked").val();
        if (show == 'data') {
            // disable the SQL comment options
            if (comments_are_present) {
                $("#checkbox_sql_dates").prop('disabled', true).parent().fadeTo('fast', 0.4);
            }
            $("#checkbox_sql_relation").prop('disabled', true).parent().fadeTo('fast', 0.4);
            $("#checkbox_sql_mime").prop('disabled', true).parent().fadeTo('fast', 0.4);
        } else {
            // enable the SQL comment options
            if (comments_are_present) {
                $("#checkbox_sql_dates").removeProp('disabled').parent().fadeTo('fast', 1);
            }
            $("#checkbox_sql_relation").removeProp('disabled').parent().fadeTo('fast', 1);
            $("#checkbox_sql_mime").removeProp('disabled').parent().fadeTo('fast', 1);
        }
     });
});


/**
 * Toggles the hiding and showing of plugin structure-specific and data-specific
 * options
 */
function toggle_structure_data_opts(pluginName)
{
    var radioFormName = pluginName + "_structure_or_data";
    var dataDiv = "#" + pluginName + "_data";
    var structureDiv = "#" + pluginName + "_structure";
    var show = $("input[type='radio'][name='" + radioFormName + "']:checked").val();
    if (show == 'data') {
        $(dataDiv).slideDown('slow');
        $(structureDiv).slideUp('slow');
    } else {
        $(structureDiv).slideDown('slow');
        if (show == 'structure') {
            $(dataDiv).slideUp('slow');
        } else {
            $(dataDiv).slideDown('slow');
        }
    }
}

AJAX.registerOnload('export.js', function () {
    $("input[type='radio'][name='latex_structure_or_data']").change(function() {
        toggle_structure_data_opts("latex");
    });
    $("input[type='radio'][name='odt_structure_or_data']").change(function() {
        toggle_structure_data_opts("odt");
    });
    $("input[type='radio'][name='texytext_structure_or_data']").change(function() {
        toggle_structure_data_opts("texytext");
    });
    $("input[type='radio'][name='htmlword_structure_or_data']").change(function() {
        toggle_structure_data_opts("htmlword");
    });
    $("input[type='radio'][name='sql_structure_or_data']").change(function() {
        toggle_structure_data_opts("sql");
    });
});

/**
 * Toggles the disabling of the "save to file" options
 */
function toggle_save_to_file()
{
    if (!$("#radio_dump_asfile").prop("checked")) {
        $("#ul_save_asfile > li").fadeTo('fast', 0.4);
        $("#ul_save_asfile > li > input").prop('disabled', true);
        $("#ul_save_asfile > li> select").prop('disabled', true);
    } else {
        $("#ul_save_asfile > li").fadeTo('fast', 1);
        $("#ul_save_asfile > li > input").removeProp('disabled');
        $("#ul_save_asfile > li> select").removeProp('disabled');
    }
}

AJAX.registerOnload('export.js', function () {
    toggle_save_to_file();
    $("input[type='radio'][name='output_format']").change(toggle_save_to_file);
});

/**
 * For SQL plugin, toggles the disabling of the "display comments" options
 */
function toggle_sql_include_comments()
{
    $("#checkbox_sql_include_comments").change(function() {
        if (!$("#checkbox_sql_include_comments").prop("checked")) {
            $("#ul_include_comments > li").fadeTo('fast', 0.4);
            $("#ul_include_comments > li > input").prop('disabled', true);
        } else {
            // If structure is not being exported, the comment options for structure should not be enabled
            if ($("#radio_sql_structure_or_data_data").prop("checked")) {
                $("#text_sql_header_comment").removeProp('disabled').parent("li").fadeTo('fast', 1);
            } else {
                $("#ul_include_comments > li").fadeTo('fast', 1);
                $("#ul_include_comments > li > input").removeProp('disabled');
            }
        }
    });
}

AJAX.registerOnload('export.js', function () {
    /**
     * For SQL plugin, if "CREATE TABLE options" is checked/unchecked, check/uncheck each of its sub-options
     */
    var $create = $("#checkbox_sql_create_table_statements");
    var $create_options = $("#ul_create_table_statements input");
    $create.change(function() {
        $create_options.prop('checked', $(this).prop("checked"));
    });
    $create_options.change(function() {
        if ($create_options.is(":checked")) {
            $create.prop('checked', true);
        }
    });

    /**
     * Disables the view output as text option if the output must be saved as a file
     */
    $("#plugins").change(function() {
        var active_plugin = $("#plugins option:selected").val();
        var force_file = $("#force_file_" + active_plugin).val();
        if (force_file == "true") {
            $("#radio_view_as_text").prop('disabled', true).parent().fadeTo('fast', 0.4);
        } else {
            $("#radio_view_as_text").removeProp('disabled').parent().fadeTo('fast', 1);
        }
    });
});

/**
 * Toggles display of options when quick and custom export are selected
 */
function toggle_quick_or_custom()
{
    if ($("#radio_custom_export").prop("checked")) {
        $("#databases_and_tables").show();
        $("#rows").show();
        $("#output").show();
        $("#format_specific_opts").show();
        $("#output_quick_export").hide();
        var selected_plugin_name = $("#plugins option:selected").val();
        $("#" + selected_plugin_name + "_options").show();
    } else {
        $("#databases_and_tables").hide();
        $("#rows").hide();
        $("#output").hide();
        $("#format_specific_opts").hide();
        $("#output_quick_export").show();
    }
}

AJAX.registerOnload('export.js', function () {
    $("input[type='radio'][name='quick_or_custom']").change(toggle_quick_or_custom);

    /**
     * Sets up the interface for Javascript-enabled browsers since the default is for
     *  Javascript-disabled browsers
     * TODO: drop non-JS behaviour
     */
    if ($("input[type='hidden'][name='export_method']").val() != "custom-no-form") {
        $("#quick_or_custom").show();
    }
    $("#scroll_to_options_msg").hide();
    $("#format_specific_opts div.format_specific_options")
    .hide()
    .css({
        "border": 0,
        "margin": 0,
        "padding": 0
    })
    .find("h3")
    .remove();
    toggle_quick_or_custom();
    toggle_structure_data_opts($("select#plugins").val());
    toggle_sql_include_comments();

    /**
     * Initially disables the "Dump some row(s)" sub-options
     */
    disable_dump_some_rows_sub_options();
    
    /**
     * Disables the "Dump some row(s)" sub-options when it is not selected
     */
    $("input[type='radio'][name='allrows']").change(function() {
        if ($("input[type='radio'][name='allrows']").prop("checked")) {
            enable_dump_some_rows_sub_options();
        } else {
            disable_dump_some_rows_sub_options();
        }
    });
});

/**
 * Disables the "Dump some row(s)" sub-options
 */
function disable_dump_some_rows_sub_options()
{
    $("label[for='limit_to']").fadeTo('fast', 0.4);
    $("label[for='limit_from']").fadeTo('fast', 0.4);
    $("input[type='text'][name='limit_to']").prop('disabled', 'disabled');
    $("input[type='text'][name='limit_from']").prop('disabled', 'disabled');
}

/**
 * Enables the "Dump some row(s)" sub-options
 */
function enable_dump_some_rows_sub_options()
{
    $("label[for='limit_to']").fadeTo('fast', 1);
    $("label[for='limit_from']").fadeTo('fast', 1);
    $("input[type='text'][name='limit_to']").prop('disabled', '');
    $("input[type='text'][name='limit_from']").prop('disabled', '');
}
