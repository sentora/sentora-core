# DOMAIN: {$vh.server_name}
<virtualhost {$vh.server_ip}:{$vh.server_port}>
ServerName {$vh.server_name}
ServerAlias {$vh.server_alias}
ServerAdmin {$vh.server_admiin}
DocumentRoot "{$vh.static_dir}"
<Directory "{$vh.static_dir}">
  Options +FollowSymLinks -Indexes
  AllowOverride All
  Require all granted
</Directory>
AddType application/x-httpd-php .php3 .php
DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm

</virtualhost>