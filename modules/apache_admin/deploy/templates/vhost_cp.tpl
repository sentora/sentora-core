# Configuration for Sentora control panel.
<VirtualHost *:{$cp.server_port}>
ServerAdmin {$cp.server_admin}
DocumentRoot "{$cp.server_root}"
ServerName {$cp.server_name}

<Directory "{$cp.server_root}">
Options +FollowSymLinks -Indexes
    AllowOverride All
    Require all granted
</Directory>

AddType application/x-httpd-php .php

ErrorLog "{$cp.log_dir}sentora-error.log" 
CustomLog "{$cp.log_dir}sentora-access.log" combined
CustomLog "{$cp.log_dir}sentora-bandwidth.log" common

{if $loaderrorpages <> "0"}
{foreach $loaderrorpages as $errorpage}
{$errorpage}
{/foreach}
{/if}

# Custom settings are loaded below this line (if any exist)
{$global_zpcustom}
</VirtualHost>