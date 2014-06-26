# Security enhancement for MySQL.
sed -i "/symbolic-links=/a \secure-file-priv=/var/tmp" /etc/my.cnf

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
