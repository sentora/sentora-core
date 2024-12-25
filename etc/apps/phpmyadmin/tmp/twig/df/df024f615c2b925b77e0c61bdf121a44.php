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

/* config/form_display/input.twig */
class __TwigTemplate_3d9fbbf91db1553cfa749e4ac91ddad9 extends Template
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
        if (($context["option_is_disabled"] ?? null)) {
            // line 2
            echo "  ";
            $context["tr_class"] = (($context["tr_class"] ?? null) . " disabled-field");
        }
        // line 4
        echo "<tr";
        if (($context["tr_class"] ?? null)) {
            echo " class=\"";
            echo twig_escape_filter($this->env, ($context["tr_class"] ?? null), "html", null, true);
            echo "\"";
        }
        echo ">
  <th>
    <label for=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
        echo "\">";
        echo ($context["name"] ?? null);
        echo "</label>

    ";
        // line 8
        if ( !twig_test_empty(($context["doc"] ?? null))) {
            // line 9
            echo "      <span class=\"doc\">
        <a href=\"";
            // line 10
            echo twig_escape_filter($this->env, ($context["doc"] ?? null), "html", null, true);
            echo "\" target=\"documentation\">";
            echo PhpMyAdmin\Html\Generator::getImage("b_help", _gettext("Documentation"));
            echo "</a>
      </span>
    ";
        }
        // line 13
        echo "
    ";
        // line 14
        if (($context["option_is_disabled"] ?? null)) {
            // line 15
            echo "      <span class=\"disabled-notice\" title=\"";
echo _gettext("This setting is disabled, it will not be applied to your configuration.");
            echo "\">
        ";
echo _gettext("Disabled");
            // line 17
            echo "      </span>
    ";
        }
        // line 19
        echo "
    ";
        // line 20
        if ( !twig_test_empty(($context["description"] ?? null))) {
            // line 21
            echo "      <small>";
            echo ($context["description"] ?? null);
            echo "</small>
    ";
        }
        // line 23
        echo "  </th>

  <td>
    ";
        // line 26
        if ((($context["type"] ?? null) == "text")) {
            // line 27
            echo "      <input type=\"text\" name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" value=\"";
            echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\" class=\"w-75";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? (" custom field-error") : (" custom"))) : (""));
            echo "\">
    ";
        } elseif ((        // line 28
($context["type"] ?? null) == "password")) {
            // line 29
            echo "      <input type=\"password\" name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" value=\"";
            echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\" class=\"w-75";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? (" custom field-error") : (" custom"))) : (""));
            echo "\" spellcheck=\"false\">
    ";
        } elseif (((        // line 30
($context["type"] ?? null) == "short_text") &&  !twig_test_iterable(($context["value"] ?? null)))) {
            // line 31
            echo "      ";
            // line 32
            echo "      <input type=\"text\" size=\"25\" name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" value=\"";
            echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\" class=\"";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? ("custom field-error") : ("custom"))) : (""));
            echo "\">
    ";
        } elseif ((        // line 33
($context["type"] ?? null) == "number_text")) {
            // line 34
            echo "      <input type=\"number\" name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" value=\"";
            echo twig_escape_filter($this->env, ($context["value"] ?? null), "html", null, true);
            echo "\" class=\"";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? ("custom field-error") : ("custom"))) : (""));
            echo "\">
    ";
        } elseif ((        // line 35
($context["type"] ?? null) == "checkbox")) {
            // line 36
            echo "      <span class=\"checkbox";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? (" custom field-error") : (" custom"))) : (""));
            echo "\">
        <input type=\"checkbox\" name=\"";
            // line 37
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\"";
            echo ((($context["value"] ?? null)) ? (" checked") : (""));
            echo ">
      </span>
    ";
        } elseif ((        // line 39
($context["type"] ?? null) == "select")) {
            // line 40
            echo "      <select name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" class=\"w-75";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? (" custom field-error") : (" custom"))) : (""));
            echo "\">
        ";
            // line 41
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["select_values"] ?? null));
            foreach ($context['_seq'] as $context["key"] => $context["val"]) {
                // line 42
                echo "          ";
                if (($context["val"] === true)) {
                    $context["val"] = _gettext("Yes");
                } elseif (($context["val"] === false)) {
                    $context["val"] = _gettext("No");
                }
                // line 43
                echo "          <option value=\"";
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\"";
                echo ((((($context["key"] === ($context["value"] ?? null)) || ((($context["value"] ?? null) === true) && ($context["key"] === 1))) || ((($context["value"] ?? null) === false) && ($context["key"] === 0)))) ? (" selected") : (""));
                echo ((twig_in_filter($context["key"], ($context["select_values_disabled"] ?? null))) ? (" disabled") : (""));
                echo ">";
                echo twig_escape_filter($this->env, $context["val"], "html", null, true);
                echo "</option>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['val'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 45
            echo "      </select>
    ";
        } elseif ((        // line 46
($context["type"] ?? null) == "list")) {
            // line 47
            echo "      <textarea cols=\"35\" rows=\"5\" name=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" id=\"";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" class=\"";
            echo (( !($context["value_is_default"] ?? null)) ? (((($context["has_errors"] ?? null)) ? ("custom field-error") : ("custom"))) : (""));
            echo "\">";
            // line 48
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["value"] ?? null));
            $context['loop'] = [
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            ];
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["key"] => $context["val"]) {
                if (($context["key"] != "wrapper_params")) {
                    echo twig_escape_filter($this->env, $context["val"], "html", null, true);
                    echo (( !twig_get_attribute($this->env, $this->source, $context["loop"], "last", [], "any", false, false, false, 48)) ? ("
") : (""));
                }
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['val'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 49
            echo "</textarea>
    ";
        }
        // line 51
        echo "
    ";
        // line 52
        if ((($context["is_setup"] ?? null) && ($context["comment"] ?? null))) {
            // line 53
            echo "      <a class=\"userprefs-comment\" title=\"";
            echo twig_escape_filter($this->env, ($context["comment"] ?? null), "html", null, true);
            echo "\">";
            echo PhpMyAdmin\Html\Generator::getImage("b_tblops", _gettext("Comment"));
            echo "</a>
    ";
        }
        // line 55
        echo "
    ";
        // line 56
        if (($context["set_value"] ?? null)) {
            // line 57
            echo "      <a class=\"set-value hide\" href=\"#";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "=";
            echo twig_escape_filter($this->env, ($context["set_value"] ?? null), "html", null, true);
            echo "\" title=\"";
            echo twig_escape_filter($this->env, twig_sprintf(_gettext("Set value: %s"), ($context["set_value"] ?? null)), "html", null, true);
            echo "\">";
            // line 58
            echo PhpMyAdmin\Html\Generator::getImage("b_edit", twig_sprintf(_gettext("Set value: %s"), ($context["set_value"] ?? null)));
            // line 59
            echo "</a>
    ";
        }
        // line 61
        echo "
    ";
        // line 62
        if (($context["show_restore_default"] ?? null)) {
            // line 63
            echo "      <a class=\"restore-default hide\" href=\"#";
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "\" title=\"";
echo _gettext("Restore default value");
            echo "\">";
            // line 64
            echo PhpMyAdmin\Html\Generator::getImage("s_reload", _gettext("Restore default value"));
            // line 65
            echo "</a>
    ";
        }
        // line 67
        echo "
    ";
        // line 69
        echo "    ";
        if (($context["has_errors"] ?? null)) {
            // line 70
            echo "      <dl class=\"inline_errors\">
        ";
            // line 71
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                // line 72
                echo "          <dd>";
                echo $context["error"];
                echo "</dd>
        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 74
            echo "      </dl>
    ";
        }
        // line 76
        echo "  </td>

  ";
        // line 78
        if ((($context["is_setup"] ?? null) &&  !(null === ($context["allows_customization"] ?? null)))) {
            // line 79
            echo "    <td class=\"userprefs-allow\" title=\"";
echo _gettext("Allow users to customize this value");
            echo "\">
      <input type=\"checkbox\" name=\"";
            // line 80
            echo twig_escape_filter($this->env, ($context["path"] ?? null), "html", null, true);
            echo "-userprefs-allow\"";
            echo ((($context["allows_customization"] ?? null)) ? (" checked") : (""));
            echo " aria-label=\"";
echo _gettext("Allow users to customize this value");
            echo "\">
    </td>
  ";
        } elseif (        // line 82
($context["is_setup"] ?? null)) {
            // line 83
            echo "    <td>&nbsp;</td>
  ";
        }
        // line 85
        echo "</tr>
";
    }

    public function getTemplateName()
    {
        return "config/form_display/input.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  362 => 85,  358 => 83,  356 => 82,  347 => 80,  342 => 79,  340 => 78,  336 => 76,  332 => 74,  323 => 72,  319 => 71,  316 => 70,  313 => 69,  310 => 67,  306 => 65,  304 => 64,  298 => 63,  296 => 62,  293 => 61,  289 => 59,  287 => 58,  279 => 57,  277 => 56,  274 => 55,  266 => 53,  264 => 52,  261 => 51,  257 => 49,  223 => 48,  215 => 47,  213 => 46,  210 => 45,  196 => 43,  189 => 42,  185 => 41,  176 => 40,  174 => 39,  165 => 37,  160 => 36,  158 => 35,  147 => 34,  145 => 33,  134 => 32,  132 => 31,  130 => 30,  119 => 29,  117 => 28,  106 => 27,  104 => 26,  99 => 23,  93 => 21,  91 => 20,  88 => 19,  84 => 17,  78 => 15,  76 => 14,  73 => 13,  65 => 10,  62 => 9,  60 => 8,  53 => 6,  43 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/input.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/config/form_display/input.twig");
    }
}
