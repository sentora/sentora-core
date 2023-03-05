# DOMAIN: {$vh.server_name}
# PORT FORWARD FROM 80 TO: 443
<virtualhost 0.0.0.0:80>
ServerName {$vh.server_name}
{if $vh.server_alias != "" }
ServerAlias {$vh.server_alias}	
{/if}
ServerAdmin {$vh.server_admiin}
RewriteEngine on
ReWriteCond %{SERVER_PORT} !^443$
RewriteRule ^/(.*) http://%{HTTP_HOST}:80/$1 [NC,R,L] 
</virtualhost>
# END DOMAIN: {$vh.server_name}
