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

/* display/results/comment_for_row.twig */
class __TwigTemplate_3e42dd2532e6bb94b364aed01a221bdb extends Template
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
        if ((twig_get_attribute($this->env, $this->source, ($context["comments_map"] ?? null), ($context["table_name"] ?? null), [], "array", true, true, false, 1) && twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 2
($context["comments_map"] ?? null), ($context["table_name"] ?? null), [], "array", false, true, false, 2), ($context["column_name"] ?? null), [], "array", true, true, false, 2))) {
            // line 3
            echo "    <br><span class=\"tblcomment\" title=\"";
            echo twig_escape_filter($this->env, (($__internal_compile_0 = (($__internal_compile_1 = ($context["comments_map"] ?? null)) && is_array($__internal_compile_1) || $__internal_compile_1 instanceof ArrayAccess ? ($__internal_compile_1[($context["table_name"] ?? null)] ?? null) : null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0[($context["column_name"] ?? null)] ?? null) : null), "html", null, true);
            echo "\">
        ";
            // line 4
            if ((twig_length_filter($this->env, (($__internal_compile_2 = (($__internal_compile_3 = ($context["comments_map"] ?? null)) && is_array($__internal_compile_3) || $__internal_compile_3 instanceof ArrayAccess ? ($__internal_compile_3[($context["table_name"] ?? null)] ?? null) : null)) && is_array($__internal_compile_2) || $__internal_compile_2 instanceof ArrayAccess ? ($__internal_compile_2[($context["column_name"] ?? null)] ?? null) : null)) > ($context["limit_chars"] ?? null))) {
                // line 5
                echo "            ";
                echo twig_escape_filter($this->env, twig_slice($this->env, (($__internal_compile_4 = (($__internal_compile_5 = ($context["comments_map"] ?? null)) && is_array($__internal_compile_5) || $__internal_compile_5 instanceof ArrayAccess ? ($__internal_compile_5[($context["table_name"] ?? null)] ?? null) : null)) && is_array($__internal_compile_4) || $__internal_compile_4 instanceof ArrayAccess ? ($__internal_compile_4[($context["column_name"] ?? null)] ?? null) : null), 0, ($context["limit_chars"] ?? null)), "html", null, true);
                echo "…
        ";
            } else {
                // line 7
                echo "            ";
                echo twig_escape_filter($this->env, (($__internal_compile_6 = (($__internal_compile_7 = ($context["comments_map"] ?? null)) && is_array($__internal_compile_7) || $__internal_compile_7 instanceof ArrayAccess ? ($__internal_compile_7[($context["table_name"] ?? null)] ?? null) : null)) && is_array($__internal_compile_6) || $__internal_compile_6 instanceof ArrayAccess ? ($__internal_compile_6[($context["column_name"] ?? null)] ?? null) : null), "html", null, true);
                echo "
        ";
            }
            // line 9
            echo "    </span>
";
        }
    }

    public function getTemplateName()
    {
        return "display/results/comment_for_row.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 9,  53 => 7,  47 => 5,  45 => 4,  40 => 3,  38 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/comment_for_row.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/display/results/comment_for_row.twig");
    }
}
