# Security enhancement for MySQL.
sed -i "/ssl-key=/a \secure-file-priv = /var/tmp" /etc/mysql/my.cnf

if ! grep -q "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" /etc/sudoers; then sed -i "s|apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|www-data ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|"  ; fi

# Double check fixing permissions for CRON jobs.
chmod -R 644 /var/spool/cron/
chmod -R 644 /etc/cron.d/

# Removal of Password Directories module
rm -Rf /etc/zpanel/panel/modules/htpasswd

# Force removal of the 'getdownload.php' file for those that failed to delete it as per our security bulletin.
rm -f /etc/zpanel/panel/modules/backupmgr/code/getdownload.php

# Re-compile the latest version of zsudo
rm -f /etc/zpanel/panel/bin/zsudo
cc -o /etc/zpanel/panel/bin/zsudo /etc/zpanel/configs/bin/zsudo.c
sudo chown root /etc/zpanel/panel/bin/zsudo
chmod +s /etc/zpanel/panel/bin/zsudo