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

/* list_navigator.twig */
class __TwigTemplate_3c1dd038cd66a14cd13e4b098acc8972 extends Template
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
        if ((($context["max_count"] ?? null) < ($context["count"] ?? null))) {
            // line 2
            echo "<div class=\"";
            echo twig_escape_filter($this->env, twig_join_filter(($context["classes"] ?? null), " "), "html", null, true);
            echo "\">
  ";
            // line 3
            if ((($context["frame"] ?? null) != "frame_navigation")) {
                // line 4
                echo "    ";
echo _gettext("Page number:");
                // line 5
                echo "  ";
            }
            // line 6
            echo "
  ";
            // line 7
            if ((($context["position"] ?? null) > 0)) {
                // line 8
                echo "    <a href=\"";
                echo ($context["script"] ?? null);
                echo "\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), [($context["param_name"] ?? null) => 0]), "", false);
                echo "\"";
                echo (((($context["frame"] ?? null) == "frame_navigation")) ? (" class=\"ajax\"") : (""));
                echo " title=\"";
echo _pgettext("First page", "Begin");
                echo "\">
      ";
                // line 9
                if (PhpMyAdmin\Util::showIcons("TableNavigationLinksMode")) {
                    // line 10
                    echo "        &lt;&lt;
      ";
                }
                // line 12
                echo "      ";
                if (PhpMyAdmin\Util::showText("TableNavigationLinksMode")) {
                    // line 13
                    echo "        ";
echo _pgettext("First page", "Begin");
                    // line 14
                    echo "      ";
                }
                // line 15
                echo "    </a>
    <a href=\"";
                // line 16
                echo ($context["script"] ?? null);
                echo "\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), [($context["param_name"] ?? null) => (($context["position"] ?? null) - ($context["max_count"] ?? null))]), "", false);
                echo "\"";
                echo (((($context["frame"] ?? null) == "frame_navigation")) ? (" class=\"ajax\"") : (""));
                echo " title=\"";
echo _pgettext("Previous page", "Previous");
                echo "\">
      ";
                // line 17
                if (PhpMyAdmin\Util::showIcons("TableNavigationLinksMode")) {
                    // line 18
                    echo "        &lt;
      ";
                }
                // line 20
                echo "      ";
                if (PhpMyAdmin\Util::showText("TableNavigationLinksMode")) {
                    // line 21
                    echo "        ";
echo _pgettext("Previous page", "Previous");
                    // line 22
                    echo "      ";
                }
                // line 23
                echo "    </a>
  ";
            }
            // line 25
            echo "
  <form action=\"";
            // line 26
            echo ($context["script"] ?? null);
            echo "\" method=\"post\">
    ";
            // line 27
            echo PhpMyAdmin\Url::getHiddenInputs(($context["url_params"] ?? null));
            echo "

    ";
            // line 29
            echo ($context["page_selector"] ?? null);
            echo "
  </form>

  ";
            // line 32
            if (((($context["position"] ?? null) + ($context["max_count"] ?? null)) < ($context["count"] ?? null))) {
                // line 33
                echo "    <a href=\"";
                echo ($context["script"] ?? null);
                echo "\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), [($context["param_name"] ?? null) => (($context["position"] ?? null) + ($context["max_count"] ?? null))]), "", false);
                echo "\"";
                echo (((($context["frame"] ?? null) == "frame_navigation")) ? (" class=\"ajax\"") : (""));
                echo " title=\"";
echo _pgettext("Next page", "Next");
                echo "\">
      ";
                // line 34
                if (PhpMyAdmin\Util::showText("TableNavigationLinksMode")) {
                    // line 35
                    echo "        ";
echo _pgettext("Next page", "Next");
                    // line 36
                    echo "      ";
                }
                // line 37
                echo "      ";
                if (PhpMyAdmin\Util::showIcons("TableNavigationLinksMode")) {
                    // line 38
                    echo "        &gt;
      ";
                }
                // line 40
                echo "    </a>
    ";
                // line 41
                $context["last_pos"] = ((int) floor((($context["count"] ?? null) / ($context["max_count"] ?? null))) * ($context["max_count"] ?? null));
                // line 42
                echo "    <a href=\"";
                echo ($context["script"] ?? null);
                echo "\" data-post=\"";
                echo PhpMyAdmin\Url::getCommon(twig_array_merge(($context["url_params"] ?? null), [($context["param_name"] ?? null) => (((($context["last_pos"] ?? null) == ($context["count"] ?? null))) ? ((($context["count"] ?? null) - ($context["max_count"] ?? null))) : (($context["last_pos"] ?? null)))]), "", false);
                echo "\"";
                echo (((($context["frame"] ?? null) == "frame_navigation")) ? (" class=\"ajax\"") : (""));
                echo " title=\"";
echo _pgettext("Last page", "End");
                echo "\">
      ";
                // line 43
                if (PhpMyAdmin\Util::showText("TableNavigationLinksMode")) {
                    // line 44
                    echo "        ";
echo _pgettext("Last page", "End");
                    // line 45
                    echo "      ";
                }
                // line 46
                echo "      ";
                if (PhpMyAdmin\Util::showIcons("TableNavigationLinksMode")) {
                    // line 47
                    echo "        &gt;&gt;
      ";
                }
                // line 49
                echo "    </a>
  ";
            }
            // line 51
            echo "</div>
";
        }
    }

    public function getTemplateName()
    {
        return "list_navigator.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  196 => 51,  192 => 49,  188 => 47,  185 => 46,  182 => 45,  179 => 44,  177 => 43,  166 => 42,  164 => 41,  161 => 40,  157 => 38,  154 => 37,  151 => 36,  148 => 35,  146 => 34,  135 => 33,  133 => 32,  127 => 29,  122 => 27,  118 => 26,  115 => 25,  111 => 23,  108 => 22,  105 => 21,  102 => 20,  98 => 18,  96 => 17,  86 => 16,  83 => 15,  80 => 14,  77 => 13,  74 => 12,  70 => 10,  68 => 9,  57 => 8,  55 => 7,  52 => 6,  49 => 5,  46 => 4,  44 => 3,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "list_navigator.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/list_navigator.twig");
    }
}
