# DOMAIN: {$vh.server_name}
<virtualhost {$vh.server_ip}:{$vh.server_port}>
ServerName {$vh.server_name}
ServerAlias {$vh.server_alias}
ServerAdmin {$vh.server_admiin}
DocumentRoot "{$vh.parking_path}"
<Directory "{$vh.parking_path}">
  Options +FollowSymLinks -Indexes
  AllowOverride All
  Require all granted
</Directory>
AddType application/x-httpd-php .php3 .php
DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm

# Custom Global Settings (if any exist)
{$vh.global_vhcustom}
# Custom VH settings (if any exist)
{$vh.vh_custom_tx}
</virtualhost>