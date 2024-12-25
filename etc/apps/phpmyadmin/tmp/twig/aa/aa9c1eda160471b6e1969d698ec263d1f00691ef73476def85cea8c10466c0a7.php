<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* select_lang.twig */
class __TwigTemplate_a83ef88e4b990af8c4ac97cbdb5b12dfd66636aef83a2f0c92cd16bb91edba0f extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "    <form method=\"get\" action=\"index.php\" class=\"disableAjax\">
    ";
        // line 2
        echo PhpMyAdmin\Url::getHiddenInputs(($context["_form_params"] ?? null));
        echo "

    ";
        // line 4
        if (($context["use_fieldset"] ?? null)) {
            // line 5
            echo "        <fieldset>
            <legend lang=\"en\" dir=\"ltr\">";
            // line 6
            echo ($context["language_title"] ?? null);
            echo "</legend>
    ";
        } else {
            // line 8
            echo "        <bdo lang=\"en\" dir=\"ltr\">
            <label for=\"sel-lang\">";
            // line 9
            echo ($context["language_title"] ?? null);
            echo "</label>
        </bdo>
    ";
        }
        // line 12
        echo "
    <select name=\"lang\" class=\"autosubmit\" lang=\"en\" dir=\"ltr\" id=\"sel-lang\">

    ";
        // line 15
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["available_languages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 16
            echo "        ";
            // line 17
            echo "        <option value=\"";
            echo twig_escape_filter($this->env, twig_lower_filter($this->env, $this->getAttribute($context["language"], "getCode", [], "method")), "html", null, true);
            echo "\"";
            // line 18
            if ($this->getAttribute($context["language"], "isActive", [], "method")) {
                // line 19
                echo "                selected=\"selected\"";
            }
            // line 21
            echo ">
        ";
            // line 22
            echo $this->getAttribute($context["language"], "getName", [], "method");
            echo "
        </option>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "
    </select>

    ";
        // line 28
        if (($context["use_fieldset"] ?? null)) {
            // line 29
            echo "        </fieldset>
    ";
        }
        // line 31
        echo "
    </form>
";
    }

    public function getTemplateName()
    {
        return "select_lang.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  100 => 31,  96 => 29,  94 => 28,  89 => 25,  80 => 22,  77 => 21,  74 => 19,  72 => 18,  68 => 17,  66 => 16,  62 => 15,  57 => 12,  51 => 9,  48 => 8,  43 => 6,  40 => 5,  38 => 4,  33 => 2,  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "select_lang.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/select_lang.twig");
    }
}
