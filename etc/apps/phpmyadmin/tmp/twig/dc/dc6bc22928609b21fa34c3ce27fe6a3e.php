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

/* config/form_display/display.twig */
class __TwigTemplate_333f4440c256d9bae3604ce533b64a88 extends Template
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
        echo "<form method=\"post\" action=\"";
        echo twig_escape_filter($this->env, ($context["action"] ?? null), "html_attr");
        echo "\" class=\"config-form disableAjax\">
  <input type=\"hidden\" name=\"tab_hash\" value=\"\">
  ";
        // line 3
        if (($context["has_check_page_refresh"] ?? null)) {
            // line 4
            echo "    <input type=\"hidden\" name=\"check_page_refresh\" id=\"check_page_refresh\" value=\"\">
  ";
        }
        // line 6
        echo "  ";
        echo PhpMyAdmin\Url::getHiddenInputs("", "", 0, "server");
        echo "
  ";
        // line 7
        echo PhpMyAdmin\Url::getHiddenFields(($context["hidden_fields"] ?? null), "", true);
        echo "

  <ul class=\"nav nav-tabs\" id=\"configFormDisplayTab\" role=\"tablist\">
    ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["tabs"] ?? null));
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
        foreach ($context['_seq'] as $context["id"] => $context["name"]) {
            // line 11
            echo "      <li class=\"nav-item\" role=\"presentation\">
        <a class=\"nav-link";
            // line 12
            echo ((twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, false, 12)) ? (" active") : (""));
            echo "\" id=\"";
            echo twig_escape_filter($this->env, $context["id"], "html", null, true);
            echo "-tab\" href=\"#";
            echo twig_escape_filter($this->env, $context["id"], "html", null, true);
            echo "\" data-bs-toggle=\"tab\" role=\"tab\" aria-controls=\"";
            echo twig_escape_filter($this->env, $context["id"], "html", null, true);
            echo "\" aria-selected=\"";
            echo ((twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, false, 12)) ? ("true") : ("false"));
            echo "\">";
            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
            echo "</a>
      </li>
    ";
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
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 15
        echo "  </ul>
  <div class=\"tab-content\">
    ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["forms"] ?? null));
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
        foreach ($context['_seq'] as $context["_key"] => $context["form"]) {
            // line 18
            echo "      <div class=\"tab-pane fade";
            echo ((twig_get_attribute($this->env, $this->source, $context["loop"], "first", [], "any", false, false, false, 18)) ? (" show active") : (""));
            echo "\" id=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["form"], "name", [], "any", false, false, false, 18), "html", null, true);
            echo "\" role=\"tabpanel\" aria-labelledby=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["form"], "name", [], "any", false, false, false, 18), "html", null, true);
            echo "-tab\">
        <div class=\"card border-top-0\">
          <div class=\"card-body\">
            <h5 class=\"card-title visually-hidden\">";
            // line 21
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["form"], "descriptions", [], "any", false, false, false, 21), "name", [], "any", false, false, false, 21), "html", null, true);
            echo "</h5>
            ";
            // line 22
            if ( !twig_test_empty(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["form"], "descriptions", [], "any", false, false, false, 22), "desc", [], "any", false, false, false, 22))) {
                // line 23
                echo "              <h6 class=\"card-subtitle mb-2 text-muted\">";
                echo twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["form"], "descriptions", [], "any", false, false, false, 23), "desc", [], "any", false, false, false, 23);
                echo "</h6>
            ";
            }
            // line 25
            echo "
            <fieldset class=\"optbox\">
              <legend>";
            // line 27
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["form"], "descriptions", [], "any", false, false, false, 27), "name", [], "any", false, false, false, 27), "html", null, true);
            echo "</legend>

              ";
            // line 30
            echo "              ";
            if ((twig_test_iterable(twig_get_attribute($this->env, $this->source, $context["form"], "errors", [], "any", false, false, false, 30)) && (twig_length_filter($this->env, twig_get_attribute($this->env, $this->source, $context["form"], "errors", [], "any", false, false, false, 30)) > 0))) {
                // line 31
                echo "                <dl class=\"errors\">
                  ";
                // line 32
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["form"], "errors", [], "any", false, false, false, 32));
                foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
                    // line 33
                    echo "                    <dd>";
                    echo twig_escape_filter($this->env, $context["error"], "html", null, true);
                    echo "</dd>
                  ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 35
                echo "                </dl>
              ";
            }
            // line 37
            echo "
              <table class=\"table table-borderless\">
                ";
            // line 39
            echo twig_get_attribute($this->env, $this->source, $context["form"], "fields_html", [], "any", false, false, false, 39);
            echo "
              </table>
            </fieldset>
          </div>

          ";
            // line 44
            if (($context["show_buttons"] ?? null)) {
                // line 45
                echo "            <div class=\"card-footer\">
              <input class=\"btn btn-primary\" type=\"submit\" name=\"submit_save\" value=\"";
echo _gettext("Apply");
                // line 46
                echo "\">
              <input class=\"btn btn-secondary\" type=\"button\" name=\"submit_reset\" value=\"";
echo _gettext("Reset");
                // line 47
                echo "\">
            </div>
          ";
            }
            // line 50
            echo "        </div>
      </div>
    ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['form'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 53
        echo "  </div>
</form>

<script type=\"text/javascript\">
  if (typeof configInlineParams === 'undefined' || !Array.isArray(configInlineParams)) {
    configInlineParams = [];
  }
  configInlineParams.push(function () {
    ";
        // line 61
        echo twig_join_filter(($context["js_array"] ?? null), ";
");
        echo ";

    \$.extend(Messages, {
      'error_nan_p': '";
        // line 64
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, _gettext("Not a positive number!"), "js"), "html", null, true);
        echo "',
      'error_nan_nneg': '";
        // line 65
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, _gettext("Not a non-negative number!"), "js"), "html", null, true);
        echo "',
      'error_incorrect_port': '";
        // line 66
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, _gettext("Not a valid port number!"), "js"), "html", null, true);
        echo "',
      'error_invalid_value': '";
        // line 67
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, _gettext("Incorrect value!"), "js"), "html", null, true);
        echo "',
      'error_value_lte': '";
        // line 68
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, _gettext("Value must be less than or equal to %s!"), "js"), "html", null, true);
        echo "',
    });

    \$.extend(defaultValues, {
      ";
        // line 72
        echo twig_join_filter(($context["js_default"] ?? null), ",
      ");
        echo "
    });
  });
  if (typeof configScriptLoaded !== 'undefined' && configInlineParams) {
    loadInlineConfig();
  }
</script>
";
    }

    public function getTemplateName()
    {
        return "config/form_display/display.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  266 => 72,  259 => 68,  255 => 67,  251 => 66,  247 => 65,  243 => 64,  236 => 61,  226 => 53,  210 => 50,  205 => 47,  201 => 46,  197 => 45,  195 => 44,  187 => 39,  183 => 37,  179 => 35,  170 => 33,  166 => 32,  163 => 31,  160 => 30,  155 => 27,  151 => 25,  145 => 23,  143 => 22,  139 => 21,  128 => 18,  111 => 17,  107 => 15,  80 => 12,  77 => 11,  60 => 10,  54 => 7,  49 => 6,  45 => 4,  43 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "config/form_display/display.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/config/form_display/display.twig");
    }
}
