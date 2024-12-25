<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* display/results/table.twig */
class __TwigTemplate_fda64d5be3f454529c2c67f8a5393558 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        ob_start(function () { return ''; });
        // line 2
        echo "  ";
        if ( !twig_test_empty(($context["navigation"] ?? null))) {
            // line 3
            echo "    <table class=\"navigation d-print-none\">
      <tr>
        <td class=\"navigation_separator\"></td>

        ";
            // line 7
            echo twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "move_backward_buttons", [], "any", false, false, false, 7);
            echo "
        ";
            // line 8
            echo twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "page_selector", [], "any", false, false, false, 8);
            echo "
        ";
            // line 9
            echo twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "move_forward_buttons", [], "any", false, false, false, 9);
            echo "

        ";
            // line 11
            if ((twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "number_total_page", [], "any", false, false, false, 11) != 1)) {
                // line 12
                echo "          <td><div class=\"navigation_separator\">|</div></td>
        ";
            }
            // line 14
            echo "
        ";
            // line 15
            if (twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "has_show_all", [], "any", false, false, false, 15)) {
                // line 16
                echo "          <td>
            <form action=\"";
                // line 17
                echo PhpMyAdmin\Url::getFromRoute("/sql");
                echo "\" method=\"post\">
              ";
                // line 18
                echo PhpMyAdmin\Url::getHiddenFields(twig_array_merge(twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "hidden_fields", [], "any", false, false, false, 18), ["session_max_rows" => twig_get_attribute($this->env, $this->source,                 // line 19
($context["navigation"] ?? null), "session_max_rows", [], "any", false, false, false, 19), "pos" => "0"]));
                // line 21
                echo "
              <input type=\"checkbox\" name=\"navig\" id=\"showAll_";
                // line 22
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\" class=\"showAllRows\" value=\"all\"";
                // line 23
                echo ((twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "is_showing_all", [], "any", false, false, false, 23)) ? (" checked") : (""));
                echo ">
              <label for=\"showAll_";
                // line 24
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\">";
echo _gettext("Show all");
                echo "</label>
            </form>
          </td>
          <td><div class=\"navigation_separator\">|</div></td>
        ";
            }
            // line 29
            echo "
        <td>
          <div class=\"save_edited hide\">
            <input class=\"btn btn-link\" type=\"submit\" value=\"";
echo _gettext("Save edited data");
            // line 32
            echo "\">
            <div class=\"navigation_separator\">|</div>
          </div>
        </td>
        <td>
          <div class=\"restore_column hide\">
            <input class=\"btn btn-link\" type=\"submit\" value=\"";
echo _gettext("Restore column order");
            // line 38
            echo "\">
            <div class=\"navigation_separator\">|</div>
          </div>
        </td>
        <td class=\"navigation_goto\">
          <form action=\"";
            // line 43
            echo PhpMyAdmin\Url::getFromRoute("/sql");
            echo "\" method=\"post\" class=\"maxRowsForm\">
            ";
            // line 44
            echo PhpMyAdmin\Url::getHiddenFields(twig_array_merge(twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "hidden_fields", [], "any", false, false, false, 44), ["pos" => twig_get_attribute($this->env, $this->source,             // line 45
($context["navigation"] ?? null), "pos", [], "any", false, false, false, 45), "unlim_num_rows" =>             // line 46
($context["unlim_num_rows"] ?? null)]));
            // line 47
            echo "

            <label for=\"sessionMaxRowsSelect\">";
echo _gettext("Number of rows:");
            // line 49
            echo "</label>
            <select class=\"autosubmit\" name=\"session_max_rows\" id=\"sessionMaxRowsSelect\">
              ";
            // line 51
            if (twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "is_showing_all", [], "any", false, false, false, 51)) {
                // line 52
                echo "                <option value=\"\" disabled selected>";
echo _gettext("All");
                echo "</option>
              ";
            }
            // line 54
            echo "              ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable([0 => "25", 1 => "50", 2 => "100", 3 => "250", 4 => "500"]);
            foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                // line 55
                echo "                <option value=\"";
                echo twig_escape_filter($this->env, $context["option"], "html", null, true);
                echo "\"";
                echo (((twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "max_rows", [], "any", false, false, false, 55) == $context["option"])) ? (" selected") : (""));
                echo ">";
                echo twig_escape_filter($this->env, $context["option"], "html", null, true);
                echo "</option>
              ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['option'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 57
            echo "            </select>
          </form>
        </td>
        <td class=\"navigation_separator\"></td>
        <td class=\"largescreenonly\">
          <span>";
echo _gettext("Filter rows");
            // line 62
            echo ":</span>
          <input type=\"text\" class=\"filter_rows\" placeholder=\"";
echo _gettext("Search this table");
            // line 64
            echo "\" data-for=\"";
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">
        </td>
        <td class=\"largescreenonly\">
          ";
            // line 67
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "sort_by_key", [], "any", false, false, false, 67))) {
                // line 68
                echo "            <form action=\"";
                echo PhpMyAdmin\Url::getFromRoute("/sql");
                echo "\" method=\"post\" class=\"d-print-none\">
              ";
                // line 69
                echo PhpMyAdmin\Url::getHiddenFields(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "sort_by_key", [], "any", false, false, false, 69), "hidden_fields", [], "any", false, false, false, 69));
                echo "
              ";
echo _gettext("Sort by key:");
                // line 71
                echo "              <select name=\"sql_query\" class=\"autosubmit\">
                ";
                // line 72
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["navigation"] ?? null), "sort_by_key", [], "any", false, false, false, 72), "options", [], "any", false, false, false, 72));
                foreach ($context['_seq'] as $context["_key"] => $context["option"]) {
                    // line 73
                    echo "                  <option value=\"";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["option"], "value", [], "any", false, false, false, 73), "html", null, true);
                    echo "\"";
                    echo ((twig_get_attribute($this->env, $this->source, $context["option"], "is_selected", [], "any", false, false, false, 73)) ? (" selected") : (""));
                    echo ">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["option"], "content", [], "any", false, false, false, 73), "html", null, true);
                    echo "</option>
                ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['option'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 75
                echo "              </select>
            </form>
          ";
            }
            // line 78
            echo "        </td>
        <td class=\"navigation_separator\"></td>
      </tr>
    </table>
  ";
        }
        $context["navigation_html"] = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
        // line 84
        echo "
";
        // line 85
        echo ($context["sql_query_message"] ?? null);
        echo "

";
        // line 87
        echo twig_escape_filter($this->env, ($context["navigation_html"] ?? null), "html", null, true);
        echo "

<input class=\"save_cells_at_once\" type=\"hidden\" value=\"";
        // line 89
        echo twig_escape_filter($this->env, ($context["save_cells_at_once"] ?? null), "html", null, true);
        echo "\">
<div class=\"common_hidden_inputs\">
  ";
        // line 91
        echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null));
        echo "
</div>

";
        // line 94
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 94))) {
            // line 95
            echo "  ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 95), "order", [], "any", false, false, false, 95)) {
                // line 96
                echo "    <input class=\"col_order\" type=\"hidden\" value=\"";
                echo twig_escape_filter($this->env, twig_join_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 96), "order", [], "any", false, false, false, 96), ","), "html", null, true);
                echo "\">
  ";
            }
            // line 98
            echo "  ";
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 98), "visibility", [], "any", false, false, false, 98)) {
                // line 99
                echo "    <input class=\"col_visib\" type=\"hidden\" value=\"";
                echo twig_escape_filter($this->env, twig_join_filter(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 99), "visibility", [], "any", false, false, false, 99), ","), "html", null, true);
                echo "\">
  ";
            }
            // line 101
            echo "  ";
            if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 101), "is_view", [], "any", false, false, false, 101)) {
                // line 102
                echo "    <input class=\"table_create_time\" type=\"hidden\" value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_order", [], "any", false, false, false, 102), "table_create_time", [], "any", false, false, false, 102), "html", null, true);
                echo "\">
  ";
            }
        }
        // line 105
        echo "
";
        // line 106
        if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 106))) {
            // line 107
            echo "  <form method=\"post\" action=\"";
            echo PhpMyAdmin\Url::getFromRoute("/sql");
            echo "\" name=\"displayOptionsForm\" class=\"ajax d-print-none\">
    ";
            // line 108
            echo PhpMyAdmin\Url::getHiddenInputs(["db" =>             // line 109
($context["db"] ?? null), "table" =>             // line 110
($context["table"] ?? null), "sql_query" =>             // line 111
($context["sql_query"] ?? null), "goto" =>             // line 112
($context["goto"] ?? null), "display_options_form" => 1]);
            // line 114
            echo "

    ";
            // line 116
            if ((($context["default_sliders_state"] ?? null) != "disabled")) {
                // line 117
                echo "    <div class=\"mb-3\">
      <button class=\"btn btn-sm btn-secondary\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#extraOptions\" aria-expanded=\"";
                // line 118
                echo (((($context["default_sliders_state"] ?? null) == "open")) ? ("true") : ("false"));
                echo "\" aria-controls=\"extraOptions\">
        ";
echo _gettext("Extra options");
                // line 120
                echo "      </button>
    </div>
    <div class=\"collapse mb-3";
                // line 122
                echo (((($context["default_sliders_state"] ?? null) == "open")) ? (" show") : (""));
                echo "\" id=\"extraOptions\">
    ";
            }
            // line 124
            echo "
      <fieldset class=\"pma-fieldset\">
        <div class=\"formelement\">
          <div>
            <input type=\"radio\" name=\"pftext\" id=\"partialFulltextRadioP";
            // line 128
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" value=\"P\"";
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 128), "pftext", [], "any", false, false, false, 128) == "P")) ? (" checked") : (""));
            echo ">
            <label for=\"partialFulltextRadioP";
            // line 129
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Partial texts");
            echo "</label>
          </div>
          <div>
            <input type=\"radio\" name=\"pftext\" id=\"partialFulltextRadioF";
            // line 132
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" value=\"F\"";
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 132), "pftext", [], "any", false, false, false, 132) == "F")) ? (" checked") : (""));
            echo ">
            <label for=\"partialFulltextRadioF";
            // line 133
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Full texts");
            echo "</label>
          </div>
        </div>

        ";
            // line 137
            if ((($context["relwork"] ?? null) && ($context["displaywork"] ?? null))) {
                // line 138
                echo "          <div class=\"formelement\">
            <div>
              <input type=\"radio\" name=\"relational_display\" id=\"relationalDisplayRadioK";
                // line 140
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\" value=\"K\"";
                echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 140), "relational_display", [], "any", false, false, false, 140) == "K")) ? (" checked") : (""));
                echo ">
              <label for=\"relationalDisplayRadioK";
                // line 141
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\">";
echo _gettext("Relational key");
                echo "</label>
            </div>
            <div>
              <input type=\"radio\" name=\"relational_display\" id=\"relationalDisplayRadioD";
                // line 144
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\" value=\"D\"";
                echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 144), "relational_display", [], "any", false, false, false, 144) == "D")) ? (" checked") : (""));
                echo ">
              <label for=\"relationalDisplayRadioD";
                // line 145
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\">";
echo _gettext("Display column for relationships");
                echo "</label>
            </div>
          </div>
        ";
            }
            // line 149
            echo "
        <div class=\"formelement\">
          <input type=\"checkbox\" name=\"display_binary\" id=\"display_binary_";
            // line 151
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\"";
            // line 152
            echo (( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 152), "display_binary", [], "any", false, false, false, 152))) ? (" checked") : (""));
            echo ">
          <label for=\"display_binary_";
            // line 153
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Show binary contents");
            echo "</label>

          <input type=\"checkbox\" name=\"display_blob\" id=\"display_blob_";
            // line 155
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\"";
            // line 156
            echo (( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 156), "display_blob", [], "any", false, false, false, 156))) ? (" checked") : (""));
            echo ">
          <label for=\"display_blob_";
            // line 157
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Show BLOB contents");
            echo "</label>
        </div>

        ";
            // line 164
            echo "        <div class=\"formelement\">
          <input type=\"checkbox\" name=\"hide_transformation\" id=\"hide_transformation_";
            // line 165
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\"";
            // line 166
            echo (( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 166), "hide_transformation", [], "any", false, false, false, 166))) ? (" checked") : (""));
            echo ">
          <label for=\"hide_transformation_";
            // line 167
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Hide browser transformation");
            echo "</label>
        </div>

        <div class=\"formelement\">
          ";
            // line 171
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 171), "possible_as_geometry", [], "any", false, false, false, 171)) {
                // line 172
                echo "            <div>
              <input type=\"radio\" name=\"geoOption\" id=\"geoOptionRadioGeom";
                // line 173
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\" value=\"GEOM\"";
                echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 173), "geo_option", [], "any", false, false, false, 173) == "GEOM")) ? (" checked") : (""));
                echo ">
              <label for=\"geoOptionRadioGeom";
                // line 174
                echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
                echo "\">";
echo _gettext("Geometry");
                echo "</label>
            </div>
          ";
            }
            // line 177
            echo "          <div>
            <input type=\"radio\" name=\"geoOption\" id=\"geoOptionRadioWkt";
            // line 178
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" value=\"WKT\"";
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 178), "geo_option", [], "any", false, false, false, 178) == "WKT")) ? (" checked") : (""));
            echo ">
            <label for=\"geoOptionRadioWkt";
            // line 179
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Well Known Text");
            echo "</label>
          </div>
          <div>
            <input type=\"radio\" name=\"geoOption\" id=\"geoOptionRadioWkb";
            // line 182
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" value=\"WKB\"";
            echo (((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "options", [], "any", false, false, false, 182), "geo_option", [], "any", false, false, false, 182) == "WKB")) ? (" checked") : (""));
            echo ">
            <label for=\"geoOptionRadioWkb";
            // line 183
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\">";
echo _gettext("Well Known Binary");
            echo "</label>
          </div>
        </div>
        <div class=\"clearfloat\"></div>
      </fieldset>

      <fieldset class=\"pma-fieldset tblFooters\">
        <input class=\"btn btn-primary\" type=\"submit\" value=\"";
echo _gettext("Go");
            // line 190
            echo "\">
      </fieldset>
    ";
            // line 192
            if ((($context["default_sliders_state"] ?? null) != "disabled")) {
                // line 193
                echo "    </div>
    ";
            }
            // line 195
            echo "  </form>
";
        }
        // line 197
        echo "
";
        // line 198
        if (twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "has_bulk_actions_form", [], "any", false, false, false, 198)) {
            // line 199
            echo "  <form method=\"post\" name=\"resultsForm\" id=\"resultsForm_";
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "\" class=\"ajax\">
    ";
            // line 200
            echo PhpMyAdmin\Url::getHiddenInputs(($context["db"] ?? null), ($context["table"] ?? null), 1);
            echo "
    <input type=\"hidden\" name=\"goto\" value=\"";
            // line 201
            echo PhpMyAdmin\Url::getFromRoute("/sql");
            echo "\">
";
        }
        // line 203
        echo "
  <div class=\"table-responsive-md\">
    <table class=\"table table-striped table-hover table-sm table_results data ajax w-auto\" data-uniqueId=\"";
        // line 205
        echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
        echo "\">

      ";
        // line 207
        echo twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "button", [], "any", false, false, false, 207);
        echo "
      ";
        // line 208
        echo twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "table_headers_for_columns", [], "any", false, false, false, 208);
        echo "
      ";
        // line 209
        echo twig_get_attribute($this->env, $this->source, ($context["headers"] ?? null), "column_at_right_side", [], "any", false, false, false, 209);
        echo "

        </tr>
      </thead>

      <tbody>
        ";
        // line 215
        echo ($context["body"] ?? null);
        echo "
      </tbody>
    </table>
  </div>

";
        // line 220
        if ( !twig_test_empty(($context["bulk_links"] ?? null))) {
            // line 221
            echo "    <div class=\"d-print-none\">
      <img class=\"selectallarrow\" src=\"";
            // line 222
            echo twig_escape_filter($this->env, $this->extensions['PhpMyAdmin\Twig\AssetExtension']->getImagePath((("arrow_" . ($context["text_dir"] ?? null)) . ".png")), "html", null, true);
            echo "\" width=\"38\" height=\"22\" alt=\"";
echo _gettext("With selected:");
            echo "\">
      <input type=\"checkbox\" id=\"resultsForm_";
            // line 223
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "_checkall\" class=\"checkall_box\" title=\"";
echo _gettext("Check all");
            echo "\">
      <label for=\"resultsForm_";
            // line 224
            echo twig_escape_filter($this->env, ($context["unique_id"] ?? null), "html", null, true);
            echo "_checkall\">";
echo _gettext("Check all");
            echo "</label>
      <em class=\"with-selected\">";
echo _gettext("With selected:");
            // line 225
            echo "</em>

      <button class=\"btn btn-link mult_submit\" type=\"submit\" name=\"submit_mult\" value=\"edit\" title=\"";
echo _gettext("Edit");
            // line 227
            echo "\">
        ";
            // line 228
            echo PhpMyAdmin\Html\Generator::getIcon("b_edit", _gettext("Edit"));
            echo "
      </button>

      <button class=\"btn btn-link mult_submit\" type=\"submit\" name=\"submit_mult\" value=\"copy\" title=\"";
echo _gettext("Copy");
            // line 231
            echo "\">
        ";
            // line 232
            echo PhpMyAdmin\Html\Generator::getIcon("b_insrow", _gettext("Copy"));
            echo "
      </button>

      <button class=\"btn btn-link mult_submit\" type=\"submit\" name=\"submit_mult\" value=\"delete\" title=\"";
echo _gettext("Delete");
            // line 235
            echo "\">
        ";
            // line 236
            echo PhpMyAdmin\Html\Generator::getIcon("b_drop", _gettext("Delete"));
            echo "
      </button>

      ";
            // line 239
            if (twig_get_attribute($this->env, $this->source, ($context["bulk_links"] ?? null), "has_export_button", [], "any", false, false, false, 239)) {
                // line 240
                echo "        <button class=\"btn btn-link mult_submit\" type=\"submit\" name=\"submit_mult\" value=\"export\" title=\"";
echo _gettext("Export");
                echo "\">
          ";
                // line 241
                echo PhpMyAdmin\Html\Generator::getIcon("b_tblexport", _gettext("Export"));
                echo "
        </button>
      ";
            }
            // line 244
            echo "    </div>

    <input type=\"hidden\" name=\"clause_is_unique\" value=\"";
            // line 246
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["bulk_links"] ?? null), "clause_is_unique", [], "any", false, false, false, 246), "html", null, true);
            echo "\">
    <input type=\"hidden\" name=\"sql_query\" value=\"";
            // line 247
            echo twig_escape_filter($this->env, ($context["sql_query"] ?? null), "html", null, true);
            echo "\">
  </form>
";
        }
        // line 250
        echo "
";
        // line 251
        echo twig_escape_filter($this->env, ($context["navigation_html"] ?? null), "html", null, true);
        echo "

";
        // line 253
        if ( !twig_test_empty(($context["operations"] ?? null))) {
            // line 254
            echo "  <fieldset class=\"pma-fieldset d-print-none\">
    <legend>";
echo _gettext("Query results operations");
            // line 255
            echo "</legend>

    ";
            // line 257
            if (twig_get_attribute($this->env, $this->source, ($context["operations"] ?? null), "has_print_link", [], "any", false, false, false, 257)) {
                // line 258
                echo "      <button type=\"button\" class=\"btn btn-link jsPrintButton\">";
                echo PhpMyAdmin\Html\Generator::getIcon("b_print", _gettext("Print"), true);
                echo "</button>

      ";
                // line 260
                echo PhpMyAdmin\Html\Generator::linkOrButton("#", null, PhpMyAdmin\Html\Generator::getIcon("b_insrow", _gettext("Copy to clipboard"), true), ["id" => "copyToClipBoard", "class" => "btn"]);
                // line 265
                echo "
    ";
            }
            // line 267
            echo "
    ";
            // line 268
            if ( !twig_get_attribute($this->env, $this->source, ($context["operations"] ?? null), "has_procedure", [], "any", false, false, false, 268)) {
                // line 269
                echo "      ";
                if (twig_get_attribute($this->env, $this->source, ($context["operations"] ?? null), "has_export_link", [], "any", false, false, false, 269)) {
                    // line 270
                    echo "        ";
                    echo PhpMyAdmin\Html\Generator::linkOrButton(PhpMyAdmin\Url::getFromRoute("/table/export"), twig_get_attribute($this->env, $this->source,                     // line 272
($context["operations"] ?? null), "url_params", [], "any", false, false, false, 272), PhpMyAdmin\Html\Generator::getIcon("b_tblexport", _gettext("Export"), true), ["class" => "btn"]);
                    // line 275
                    echo "

        ";
                    // line 277
                    echo PhpMyAdmin\Html\Generator::linkOrButton(PhpMyAdmin\Url::getFromRoute("/table/chart"), twig_get_attribute($this->env, $this->source,                     // line 279
($context["operations"] ?? null), "url_params", [], "any", false, false, false, 279), PhpMyAdmin\Html\Generator::getIcon("b_chart", _gettext("Display chart"), true), ["class" => "btn"]);
                    // line 282
                    echo "

        ";
                    // line 284
                    if (twig_get_attribute($this->env, $this->source, ($context["operations"] ?? null), "has_geometry", [], "any", false, false, false, 284)) {
                        // line 285
                        echo "          ";
                        echo PhpMyAdmin\Html\Generator::linkOrButton(PhpMyAdmin\Url::getFromRoute("/table/gis-visualization"), twig_get_attribute($this->env, $this->source,                         // line 287
($context["operations"] ?? null), "url_params", [], "any", false, false, false, 287), PhpMyAdmin\Html\Generator::getIcon("b_globe", _gettext("Visualize GIS data"), true), ["class" => "btn"]);
                        // line 290
                        echo "
        ";
                    }
                    // line 292
                    echo "      ";
                }
                // line 293
                echo "
      <span>
        ";
                // line 295
                echo PhpMyAdmin\Html\Generator::linkOrButton(PhpMyAdmin\Url::getFromRoute("/view/create"), ["db" =>                 // line 297
($context["db"] ?? null), "table" => ($context["table"] ?? null), "sql_query" => ($context["sql_query"] ?? null), "printview" => true], PhpMyAdmin\Html\Generator::getIcon("b_view_add", _gettext("Create view"), true), ["class" => "btn create_view ajax"]);
                // line 300
                echo "
      </span>
    ";
            }
            // line 303
            echo "  </fieldset>
";
        }
        // line 305
        if (( !twig_test_empty(($context["operations"] ?? null)) &&  !twig_get_attribute($this->env, $this->source, ($context["operations"] ?? null), "has_procedure", [], "any", false, false, false, 305))) {
            // line 306
            echo twig_include($this->env, $context, "modals/create_view.twig");
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "display/results/table.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  739 => 306,  737 => 305,  733 => 303,  728 => 300,  726 => 297,  725 => 295,  721 => 293,  718 => 292,  714 => 290,  712 => 287,  710 => 285,  708 => 284,  704 => 282,  702 => 279,  701 => 277,  697 => 275,  695 => 272,  693 => 270,  690 => 269,  688 => 268,  685 => 267,  681 => 265,  679 => 260,  673 => 258,  671 => 257,  667 => 255,  663 => 254,  661 => 253,  656 => 251,  653 => 250,  647 => 247,  643 => 246,  639 => 244,  633 => 241,  628 => 240,  626 => 239,  620 => 236,  617 => 235,  610 => 232,  607 => 231,  600 => 228,  597 => 227,  592 => 225,  585 => 224,  579 => 223,  573 => 222,  570 => 221,  568 => 220,  560 => 215,  551 => 209,  547 => 208,  543 => 207,  538 => 205,  534 => 203,  529 => 201,  525 => 200,  520 => 199,  518 => 198,  515 => 197,  511 => 195,  507 => 193,  505 => 192,  501 => 190,  488 => 183,  482 => 182,  474 => 179,  468 => 178,  465 => 177,  457 => 174,  451 => 173,  448 => 172,  446 => 171,  437 => 167,  433 => 166,  430 => 165,  427 => 164,  419 => 157,  415 => 156,  412 => 155,  405 => 153,  401 => 152,  398 => 151,  394 => 149,  385 => 145,  379 => 144,  371 => 141,  365 => 140,  361 => 138,  359 => 137,  350 => 133,  344 => 132,  336 => 129,  330 => 128,  324 => 124,  319 => 122,  315 => 120,  310 => 118,  307 => 117,  305 => 116,  301 => 114,  299 => 112,  298 => 111,  297 => 110,  296 => 109,  295 => 108,  290 => 107,  288 => 106,  285 => 105,  278 => 102,  275 => 101,  269 => 99,  266 => 98,  260 => 96,  257 => 95,  255 => 94,  249 => 91,  244 => 89,  239 => 87,  234 => 85,  231 => 84,  223 => 78,  218 => 75,  205 => 73,  201 => 72,  198 => 71,  193 => 69,  188 => 68,  186 => 67,  179 => 64,  175 => 62,  167 => 57,  154 => 55,  149 => 54,  143 => 52,  141 => 51,  137 => 49,  132 => 47,  130 => 46,  129 => 45,  128 => 44,  124 => 43,  117 => 38,  108 => 32,  102 => 29,  92 => 24,  88 => 23,  85 => 22,  82 => 21,  80 => 19,  79 => 18,  75 => 17,  72 => 16,  70 => 15,  67 => 14,  63 => 12,  61 => 11,  56 => 9,  52 => 8,  48 => 7,  42 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/table.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/display/results/table.twig");
    }
}
