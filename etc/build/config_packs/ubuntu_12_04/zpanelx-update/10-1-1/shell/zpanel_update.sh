# Security enhancement for MySQL.
sed -i "/ssl-key=/a \secure-file-priv = /var/tmp" /etc/mysql/my.cnf

if ! grep -q "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" /etc/sudoers; then sed -i "s|apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|www-data ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|"  ; fi

# Double check fixing permissions for CRON jobs.
chmod -R 644 /var/spool/cron/
chmod -R 644 /etc/cron.d/


