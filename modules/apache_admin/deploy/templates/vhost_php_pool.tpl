[{$username}]

user = {$username}
group = {$user_group}

listen = {$php_sock}

{if $webserver_user }
listen.owner = apache
listen.group = apache
; Restrict permissions to user and group only
listen.mode = 0660
{/if }

listen.allowed_clients = 127.0.0.1

pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

chdir = /

php_admin_value[error_log] = /var/sentora/logs/php-fpm/fpm-php.log
php_admin_flag[log_errors] = on
php_admin_value[memory_limit] = 256M
php_admin_value[post_max_size] = 32M
php_admin_value[upload_max_filesize] = 32M