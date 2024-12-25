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

/* navigation/tree/path.twig */
class __TwigTemplate_b7047152d0d6ebae285edf248eba5dc6 extends Template
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
        echo "<div class='list_container hide'>
  <ul";
        // line 2
        echo ((($context["has_search_results"] ?? null)) ? (" class=\"search_results\"") : (""));
        echo ">
    ";
        // line 3
        echo ($context["list_content"] ?? null);
        echo "
  </ul>

  ";
        // line 6
        if ( !($context["is_tree"] ?? null)) {
            // line 7
            echo "    <span class='hide loaded_db'>";
            echo twig_escape_filter($this->env, twig_urlencode_filter(($context["parent_name"] ?? null)), "html", null, true);
            echo "</span>
    ";
            // line 8
            if (twig_test_empty(($context["list_content"] ?? null))) {
                // line 9
                echo "      <div>";
echo _gettext("No tables found in database.");
                echo "</div>
    ";
            }
            // line 11
            echo "  ";
        }
        // line 12
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "navigation/tree/path.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 12,  65 => 11,  59 => 9,  57 => 8,  52 => 7,  50 => 6,  44 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "navigation/tree/path.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/navigation/tree/path.twig");
    }
}
