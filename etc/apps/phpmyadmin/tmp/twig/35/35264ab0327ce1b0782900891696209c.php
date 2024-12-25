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

/* display/results/table_headers_for_columns.twig */
class __TwigTemplate_32e55a00d9b35bbdcefad412762a5c90 extends Template
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
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["columns"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["column"]) {
            // line 2
            echo "  <th class=\"draggable position-sticky";
            echo ((twig_get_attribute($this->env, $this->source, $context["column"], "is_column_numeric", [], "any", false, false, false, 2)) ? (" text-end") : (""));
            echo ((twig_get_attribute($this->env, $this->source, $context["column"], "is_column_hidden", [], "any", false, false, false, 2)) ? (" hide") : (""));
            // line 3
            echo ((($context["is_sortable"] ?? null)) ? (" column_heading") : (""));
            echo (((($context["is_sortable"] ?? null) && twig_get_attribute($this->env, $this->source, $context["column"], "is_browse_marker_enabled", [], "any", false, false, false, 3))) ? (" marker") : (""));
            echo (((($context["is_sortable"] ?? null) && twig_get_attribute($this->env, $this->source, $context["column"], "is_browse_pointer_enabled", [], "any", false, false, false, 3))) ? (" pointer") : (""));
            // line 4
            echo ((( !($context["is_sortable"] ?? null) && twig_get_attribute($this->env, $this->source, $context["column"], "has_condition", [], "any", false, false, false, 4))) ? (" condition") : (""));
            echo "\" data-column=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["column"], "column_name", [], "any", false, false, false, 4), "html", null, true);
            echo "\">
    ";
            // line 5
            if (($context["is_sortable"] ?? null)) {
                // line 6
                echo "      ";
                echo twig_get_attribute($this->env, $this->source, $context["column"], "order_link", [], "any", false, false, false, 6);
                echo "
    ";
            } else {
                // line 8
                echo "      ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["column"], "column_name", [], "any", false, false, false, 8), "html", null, true);
                echo "
    ";
            }
            // line 10
            echo "    ";
            echo twig_get_attribute($this->env, $this->source, $context["column"], "comments", [], "any", false, false, false, 10);
            echo "
  </th>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['column'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "display/results/table_headers_for_columns.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  69 => 10,  63 => 8,  57 => 6,  55 => 5,  49 => 4,  45 => 3,  41 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "display/results/table_headers_for_columns.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/display/results/table_headers_for_columns.twig");
    }
}
