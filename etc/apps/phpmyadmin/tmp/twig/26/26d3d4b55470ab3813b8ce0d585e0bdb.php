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

/* home/index.twig */
class __TwigTemplate_ecb0d8ffe2da85350d73930389836611 extends Template
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
        if (($context["is_git_revision"] ?? null)) {
            // line 2
            echo "  <div id=\"is_git_revision\"></div>
";
        }
        // line 4
        echo "
";
        // line 5
        echo ($context["message"] ?? null);
        echo "

";
        // line 7
        echo ($context["partial_logout"] ?? null);
        echo "

<div id=\"maincontainer\">
  ";
        // line 10
        echo ($context["sync_favorite_tables"] ?? null);
        echo "
  <div class=\"container-fluid\">
    <div class=\"row mb-3\">
      <div class=\"col-lg-7 col-12\">
        ";
        // line 14
        if (($context["has_server"] ?? null)) {
            // line 15
            echo "          ";
            if (($context["is_demo"] ?? null)) {
                // line 16
                echo "            <div class=\"card mt-4\">
              <div class=\"card-header\">
                ";
echo _gettext("phpMyAdmin Demo Server");
                // line 19
                echo "              </div>
              <div class=\"card-body\">
                ";
                // line 21
                ob_start(function () { return ''; });
                // line 22
                echo "                  ";
echo _gettext("You are using the demo server. You can do anything here, but please do not change root, debian-sys-maint and pma users. More information is available at %s.");
                // line 25
                echo "                ";
                $___internal_parse_0_ = ('' === $tmp = ob_get_clean()) ? '' : new Markup($tmp, $this->env->getCharset());
                // line 21
                echo twig_sprintf($___internal_parse_0_, "<a href=\"url.php?url=https://demo.phpmyadmin.net/\" target=\"_blank\" rel=\"noopener noreferrer\">demo.phpmyadmin.net</a>");
                // line 26
                echo "              </div>
            </div>
          ";
            }
            // line 29
            echo "
            <div class=\"card mt-4\">
              <div class=\"card-header\">
                ";
echo _gettext("General settings");
            // line 33
            echo "              </div>
              <ul class=\"list-group list-group-flush\">
                ";
            // line 35
            if (($context["has_server_selection"] ?? null)) {
                // line 36
                echo "                  <li id=\"li_select_server\" class=\"list-group-item\">
                    ";
                // line 37
                echo PhpMyAdmin\Html\Generator::getImage("s_host");
                echo "
                    ";
                // line 38
                echo ($context["server_selection"] ?? null);
                echo "
                  </li>
                ";
            }
            // line 41
            echo "
                ";
            // line 42
            if ((($context["server"] ?? null) > 0)) {
                // line 43
                echo "                  ";
                if (($context["has_change_password_link"] ?? null)) {
                    // line 44
                    echo "                    <li id=\"li_change_password\" class=\"list-group-item\">
                      <a href=\"";
                    // line 45
                    echo PhpMyAdmin\Url::getFromRoute("/user-password");
                    echo "\" id=\"change_password_anchor\" class=\"ajax\">
                        ";
                    // line 46
                    echo PhpMyAdmin\Html\Generator::getIcon("s_passwd", _gettext("Change password"), true);
                    echo "
                      </a>
                    </li>
                  ";
                }
                // line 50
                echo "
                  <li id=\"li_select_mysql_collation\" class=\"list-group-item\">
                    <form method=\"post\" action=\"";
                // line 52
                echo PhpMyAdmin\Url::getFromRoute("/collation-connection");
                echo "\" class=\"row row-cols-lg-auto align-items-center disableAjax\">
                      ";
                // line 53
                echo PhpMyAdmin\Url::getHiddenInputs(null, null, 4, "collation_connection");
                echo "
                      <div class=\"col-12\">
                        <label for=\"collationConnectionSelect\" class=\"col-form-label\">
                          ";
                // line 56
                echo PhpMyAdmin\Html\Generator::getImage("s_asci");
                echo "
                          ";
echo _gettext("Server connection collation:");
                // line 58
                echo "                          ";
                echo PhpMyAdmin\Html\MySQLDocumentation::show("charset-connection");
                echo "
                        </label>
                      </div>
                      ";
                // line 61
                if ( !twig_test_empty(($context["charsets"] ?? null))) {
                    // line 62
                    echo "                      <div class=\"col-12\">
                        <select lang=\"en\" dir=\"ltr\" name=\"collation_connection\" id=\"collationConnectionSelect\" class=\"form-select autosubmit\">
                          <option value=\"\">";
echo _gettext("Collation");
                    // line 64
                    echo "</option>
                          <option value=\"\"></option>
                          ";
                    // line 66
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(($context["charsets"] ?? null));
                    foreach ($context['_seq'] as $context["_key"] => $context["charset"]) {
                        // line 67
                        echo "                            <optgroup label=\"";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "name", [], "any", false, false, false, 67), "html", null, true);
                        echo "\" title=\"";
                        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["charset"], "description", [], "any", false, false, false, 67), "html", null, true);
                        echo "\">
                              ";
                        // line 68
                        $context['_parent'] = $context;
                        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["charset"], "collations", [], "any", false, false, false, 68));
                        foreach ($context['_seq'] as $context["_key"] => $context["collation"]) {
                            // line 69
                            echo "                                <option value=\"";
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["collation"], "name", [], "any", false, false, false, 69), "html", null, true);
                            echo "\" title=\"";
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["collation"], "description", [], "any", false, false, false, 69), "html", null, true);
                            echo "\"";
                            echo ((twig_get_attribute($this->env, $this->source, $context["collation"], "is_selected", [], "any", false, false, false, 69)) ? (" selected") : (""));
                            echo ">";
                            // line 70
                            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["collation"], "name", [], "any", false, false, false, 70), "html", null, true);
                            // line 71
                            echo "</option>
                              ";
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['collation'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent) + $_parent;
                        // line 73
                        echo "                            </optgroup>
                          ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['charset'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 75
                    echo "                        </select>
                      </div>
                      ";
                }
                // line 78
                echo "                    </form>
                  </li>

                  <li id=\"li_user_preferences\" class=\"list-group-item\">
                    <a href=\"";
                // line 82
                echo PhpMyAdmin\Url::getFromRoute("/preferences/manage");
                echo "\">
                      ";
                // line 83
                echo PhpMyAdmin\Html\Generator::getIcon("b_tblops", _gettext("More settings"), true);
                echo "
                    </a>
                  </li>
                ";
            }
            // line 87
            echo "              </ul>
            </div>
          ";
        }
        // line 90
        echo "
            <div class=\"card mt-4\">
              <div class=\"card-header\">
                ";
echo _gettext("Appearance settings");
        // line 94
        echo "              </div>
              <ul class=\"list-group list-group-flush\">
                ";
        // line 96
        if ( !twig_test_empty(($context["available_languages"] ?? null))) {
            // line 97
            echo "                  <li id=\"li_select_lang\" class=\"list-group-item\">
                    <form method=\"get\" action=\"";
            // line 98
            echo PhpMyAdmin\Url::getFromRoute("/");
            echo "\" class=\"row row-cols-lg-auto align-items-center disableAjax\">
                      ";
            // line 99
            echo PhpMyAdmin\Url::getHiddenInputs(["db" => ($context["db"] ?? null), "table" => ($context["table"] ?? null)]);
            echo "
                      <div class=\"col-12\">
                        <label for=\"languageSelect\" class=\"col-form-label text-nowrap\">
                          ";
            // line 102
            echo PhpMyAdmin\Html\Generator::getImage("s_lang");
            echo "
                          ";
echo _gettext("Language");
            // line 104
            echo "                          ";
            if ((_gettext("Language") != "Language")) {
                // line 105
                echo "                            ";
                // line 107
                echo "                            <i lang=\"en\" dir=\"ltr\">(Language)</i>
                          ";
            }
            // line 109
            echo "                          ";
            echo PhpMyAdmin\Html\MySQLDocumentation::showDocumentation("faq", "faq7-2");
            echo "
                        </label>
                      </div>
                      <div class=\"col-12\">
                        <select name=\"lang\" class=\"form-select autosubmit w-auto\" lang=\"en\" dir=\"ltr\" id=\"languageSelect\">
                          ";
            // line 114
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["available_languages"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
                // line 115
                echo "                            <option value=\"";
                echo twig_escape_filter($this->env, twig_lower_filter($this->env, twig_get_attribute($this->env, $this->source, $context["language"], "getCode", [], "method", false, false, false, 115)), "html", null, true);
                echo "\"";
                echo ((twig_get_attribute($this->env, $this->source, $context["language"], "isActive", [], "method", false, false, false, 115)) ? (" selected") : (""));
                echo ">";
                // line 116
                echo twig_get_attribute($this->env, $this->source, $context["language"], "getName", [], "method", false, false, false, 116);
                // line 117
                echo "</option>
                          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 119
            echo "                        </select>
                      </div>
                    </form>
                  </li>
                ";
        }
        // line 124
        echo "
                ";
        // line 125
        if (($context["has_theme_manager"] ?? null)) {
            // line 126
            echo "                  <li id=\"li_select_theme\" class=\"list-group-item\">
                    <form method=\"post\" action=\"";
            // line 127
            echo PhpMyAdmin\Url::getFromRoute("/themes/set");
            echo "\" class=\"row row-cols-lg-auto align-items-center disableAjax\">
                      ";
            // line 128
            echo PhpMyAdmin\Url::getHiddenInputs();
            echo "
                      <div class=\"col-12\">
                        <label for=\"themeSelect\" class=\"col-form-label\">
                          ";
            // line 131
            echo PhpMyAdmin\Html\Generator::getIcon("s_theme", _gettext("Theme"));
            echo "
                        </label>
                      </div>
                      <div class=\"col-12\">
                        <div class=\"input-group\">
                          <select name=\"set_theme\" class=\"form-select autosubmit\" lang=\"en\" dir=\"ltr\" id=\"themeSelect\">
                            ";
            // line 137
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["themes"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["theme"]) {
                // line 138
                echo "                              <option value=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["theme"], "id", [], "any", false, false, false, 138), "html", null, true);
                echo "\"";
                echo ((twig_get_attribute($this->env, $this->source, $context["theme"], "is_active", [], "any", false, false, false, 138)) ? (" selected") : (""));
                echo ">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["theme"], "name", [], "any", false, false, false, 138), "html", null, true);
                echo "</option>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['theme'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 140
            echo "                          </select>
                          <button type=\"button\" class=\"btn btn-outline-secondary\" data-bs-toggle=\"modal\" data-bs-target=\"#themesModal\">
                            ";
echo _pgettext("View all themes", "View all");
            // line 143
            echo "                          </button>
                        </div>
                      </div>
                    </form>
                  </li>
                ";
        }
        // line 149
        echo "              </ul>
            </div>
          </div>

      <div class=\"col-lg-5 col-12\">
        ";
        // line 154
        if ( !twig_test_empty(($context["database_server"] ?? null))) {
            // line 155
            echo "          <div class=\"card mt-4\">
            <div class=\"card-header\">
              ";
echo _gettext("Database server");
            // line 158
            echo "            </div>
            <ul class=\"list-group list-group-flush\">
              <li class=\"list-group-item\">
                ";
echo _gettext("Server:");
            // line 162
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "host", [], "any", false, false, false, 162), "html", null, true);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("Server type:");
            // line 166
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "type", [], "any", false, false, false, 166), "html", null, true);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("Server connection:");
            // line 170
            echo "                ";
            echo twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "connection", [], "any", false, false, false, 170);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("Server version:");
            // line 174
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "version", [], "any", false, false, false, 174), "html", null, true);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("Protocol version:");
            // line 178
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "protocol", [], "any", false, false, false, 178), "html", null, true);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("User:");
            // line 182
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "user", [], "any", false, false, false, 182), "html", null, true);
            echo "
              </li>
              <li class=\"list-group-item\">
                ";
echo _gettext("Server charset:");
            // line 186
            echo "                <span lang=\"en\" dir=\"ltr\">
                  ";
            // line 187
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["database_server"] ?? null), "charset", [], "any", false, false, false, 187), "html", null, true);
            echo "
                </span>
              </li>
            </ul>
          </div>
        ";
        }
        // line 193
        echo "
        ";
        // line 194
        if (( !twig_test_empty(($context["web_server"] ?? null)) || ($context["show_php_info"] ?? null))) {
            // line 195
            echo "          <div class=\"card mt-4\">
            <div class=\"card-header\">
              ";
echo _gettext("Web server");
            // line 198
            echo "            </div>
            <ul class=\"list-group list-group-flush\">
              ";
            // line 200
            if ( !twig_test_empty(($context["web_server"] ?? null))) {
                // line 201
                echo "                ";
                if ( !(null === twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "software", [], "any", false, false, false, 201))) {
                    // line 202
                    echo "                <li class=\"list-group-item\">
                  ";
                    // line 203
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "software", [], "any", false, false, false, 203), "html", null, true);
                    echo "
                </li>
                ";
                }
                // line 206
                echo "                <li class=\"list-group-item\" id=\"li_mysql_client_version\">
                  ";
echo _gettext("Database client version:");
                // line 208
                echo "                  ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "database", [], "any", false, false, false, 208), "html", null, true);
                echo "
                </li>
                <li class=\"list-group-item\">
                  ";
echo _gettext("PHP extension:");
                // line 212
                echo "                  ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "php_extensions", [], "any", false, false, false, 212));
                foreach ($context['_seq'] as $context["_key"] => $context["extension"]) {
                    // line 213
                    echo "                    ";
                    echo twig_escape_filter($this->env, $context["extension"], "html", null, true);
                    echo "
                    ";
                    // line 214
                    echo PhpMyAdmin\Html\Generator::showPHPDocumentation((("book." . $context["extension"]) . ".php"));
                    echo "
                  ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['extension'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 216
                echo "                </li>
                <li class=\"list-group-item\">
                  ";
echo _gettext("PHP version:");
                // line 219
                echo "                  ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["web_server"] ?? null), "php_version", [], "any", false, false, false, 219), "html", null, true);
                echo "
                </li>
              ";
            }
            // line 222
            echo "              ";
            if (($context["show_php_info"] ?? null)) {
                // line 223
                echo "                <li class=\"list-group-item\">
                  <a href=\"";
                // line 224
                echo PhpMyAdmin\Url::getFromRoute("/phpinfo");
                echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
                    ";
echo _gettext("Show PHP information");
                // line 226
                echo "                  </a>
                </li>
              ";
            }
            // line 229
            echo "            </ul>
          </div>
        ";
        }
        // line 232
        echo "
          <div class=\"card mt-4\">
            <div class=\"card-header\">
              phpMyAdmin
            </div>
            <ul class=\"list-group list-group-flush\">
              <li id=\"li_pma_version\" class=\"list-group-item";
        // line 238
        echo ((($context["is_version_checked"] ?? null)) ? (" jsversioncheck") : (""));
        echo "\">
                ";
echo _gettext("Version information:");
        // line 240
        echo "                <span class=\"version\">";
        echo twig_escape_filter($this->env, ($context["phpmyadmin_version"] ?? null), "html", null, true);
        echo "</span>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 243
        echo PhpMyAdmin\Html\MySQLDocumentation::getDocumentationLink("index");
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
                  ";
echo _gettext("Documentation");
        // line 245
        echo "                </a>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 248
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
                  ";
echo _gettext("Official Homepage");
        // line 250
        echo "                </a>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 253
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/contribute/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
                  ";
echo _gettext("Contribute");
        // line 255
        echo "                </a>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 258
        echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/support/"), "html", null, true);
        echo "\" target=\"_blank\" rel=\"noopener noreferrer\">
                  ";
echo _gettext("Get support");
        // line 260
        echo "                </a>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 263
        echo PhpMyAdmin\Url::getFromRoute("/changelog");
        echo "\" target=\"_blank\">
                  ";
echo _gettext("List of changes");
        // line 265
        echo "                </a>
              </li>
              <li class=\"list-group-item\">
                <a href=\"";
        // line 268
        echo PhpMyAdmin\Url::getFromRoute("/license");
        echo "\" target=\"_blank\">
                  ";
echo _gettext("License");
        // line 270
        echo "                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      ";
        // line 277
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["errors"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["error"]) {
            // line 278
            echo "        <div class=\"alert ";
            echo (((twig_get_attribute($this->env, $this->source, $context["error"], "severity", [], "any", false, false, false, 278) == "warning")) ? ("alert-warning") : ("alert-info"));
            echo "\" role=\"alert\">
          ";
            // line 279
            if ((twig_get_attribute($this->env, $this->source, $context["error"], "severity", [], "any", false, false, false, 279) == "warning")) {
                // line 280
                echo "            ";
                echo PhpMyAdmin\Html\Generator::getImage("s_attention", _gettext("Warning"));
                echo "
          ";
            } else {
                // line 282
                echo "            ";
                echo PhpMyAdmin\Html\Generator::getImage("s_notice", _gettext("Notice"));
                echo "
          ";
            }
            // line 284
            echo "          ";
            echo PhpMyAdmin\Sanitize::sanitizeMessage(twig_get_attribute($this->env, $this->source, $context["error"], "message", [], "any", false, false, false, 284));
            echo "
        </div>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['error'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 287
        echo "    </div>
  </div>

";
        // line 290
        if (($context["has_theme_manager"] ?? null)) {
            // line 291
            echo "  <div class=\"modal fade\" id=\"themesModal\" tabindex=\"-1\" aria-labelledby=\"themesModalLabel\" aria-hidden=\"true\">
    <div class=\"modal-dialog modal-xl\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <h5 class=\"modal-title\" id=\"themesModalLabel\">";
echo _gettext("phpMyAdmin Themes");
            // line 295
            echo "</h5>
          <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"";
echo _gettext("Close");
            // line 296
            echo "\"></button>
        </div>
        <div class=\"modal-body\">
          <div class=\"spinner-border\" role=\"status\">
            <span class=\"visually-hidden\">";
echo _gettext("Loading…");
            // line 300
            echo "</span>
          </div>
        </div>
        <div class=\"modal-footer\">
          <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">";
echo _gettext("Close");
            // line 304
            echo "</button>
          <a href=\"";
            // line 305
            echo twig_escape_filter($this->env, PhpMyAdmin\Core::linkURL("https://www.phpmyadmin.net/themes/"), "html", null, true);
            echo "#pma_";
            echo twig_escape_filter($this->env, twig_replace_filter(($context["phpmyadmin_major_version"] ?? null), ["." => "_"]), "html", null, true);
            echo "\" class=\"btn btn-primary\" rel=\"noopener noreferrer\" target=\"_blank\">
            ";
echo _gettext("Get more themes!");
            // line 307
            echo "          </a>
        </div>
      </div>
    </div>
  </div>
";
        }
        // line 313
        echo "
";
        // line 314
        echo ($context["config_storage_message"] ?? null);
        echo "
";
    }

    public function getTemplateName()
    {
        return "home/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  715 => 314,  712 => 313,  704 => 307,  697 => 305,  694 => 304,  687 => 300,  680 => 296,  676 => 295,  669 => 291,  667 => 290,  662 => 287,  652 => 284,  646 => 282,  640 => 280,  638 => 279,  633 => 278,  629 => 277,  620 => 270,  615 => 268,  610 => 265,  605 => 263,  600 => 260,  595 => 258,  590 => 255,  585 => 253,  580 => 250,  575 => 248,  570 => 245,  565 => 243,  558 => 240,  553 => 238,  545 => 232,  540 => 229,  535 => 226,  530 => 224,  527 => 223,  524 => 222,  517 => 219,  512 => 216,  504 => 214,  499 => 213,  494 => 212,  486 => 208,  482 => 206,  476 => 203,  473 => 202,  470 => 201,  468 => 200,  464 => 198,  459 => 195,  457 => 194,  454 => 193,  445 => 187,  442 => 186,  434 => 182,  426 => 178,  418 => 174,  410 => 170,  402 => 166,  394 => 162,  388 => 158,  383 => 155,  381 => 154,  374 => 149,  366 => 143,  361 => 140,  348 => 138,  344 => 137,  335 => 131,  329 => 128,  325 => 127,  322 => 126,  320 => 125,  317 => 124,  310 => 119,  303 => 117,  301 => 116,  295 => 115,  291 => 114,  282 => 109,  278 => 107,  276 => 105,  273 => 104,  268 => 102,  262 => 99,  258 => 98,  255 => 97,  253 => 96,  249 => 94,  243 => 90,  238 => 87,  231 => 83,  227 => 82,  221 => 78,  216 => 75,  209 => 73,  202 => 71,  200 => 70,  192 => 69,  188 => 68,  181 => 67,  177 => 66,  173 => 64,  168 => 62,  166 => 61,  159 => 58,  154 => 56,  148 => 53,  144 => 52,  140 => 50,  133 => 46,  129 => 45,  126 => 44,  123 => 43,  121 => 42,  118 => 41,  112 => 38,  108 => 37,  105 => 36,  103 => 35,  99 => 33,  93 => 29,  88 => 26,  86 => 21,  83 => 25,  80 => 22,  78 => 21,  74 => 19,  69 => 16,  66 => 15,  64 => 14,  57 => 10,  51 => 7,  46 => 5,  43 => 4,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "home/index.twig", "/etc/sentora/panel/etc/apps/phpmyadmin/templates/home/index.twig");
    }
}
