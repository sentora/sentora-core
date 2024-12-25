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

/* recent_favorite_table_no_tables.twig */
class __TwigTemplate_1578ca7a9b767160f258021e3c2213d7 extends Template
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
        echo "<li class=\"warp_link\">
    ";
        // line 2
        if (($context["is_recent"] ?? null)) {
            // line 3
            echo "        ";
echo _gettext("There are no recent tables.");
            // line 4
            echo "    ";
        } else {
            // line 5
            echo "        ";
echo _gettext("There are no favorite tables.");
            // line 6
            echo "    ";
        }
        // line 7
        echo "</li>
";
    }

    public function getTemplateName()
    {
        return "recent_favorite_table_no_tables.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 7,  51 => 6,  48 => 5,  45 => 4,  42 => 3,  40 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "recent_favorite_table_no_tables.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/recent_favorite_table_no_tables.twig");
    }
}
