# Security enhancement for MySQL.
sed -i "/symbolic-links=/a \secure-file-priv=/var/tmp" /etc/my.cnf

# Double check fixing permissions for CRON jobs.
chmod -R 644 /var/spool/cron/
chmod -R 644 /etc/cron.d/

# Removal of Password Directories module
rm -Rf /etc/zpanel/panel/modules/htpasswd