#!/bin/bash

# password are set to : vagrant 
# root p -> vagrant
# mysql p -> vagrant , etc

# note that you will need to run the provision if u restart because of apache not seeing 
# the zpanle v-host file due to late virtual folder mount : vagrant provision
if [ ! -f /zplock ]; then
###
apt-get remove apparmor -y && apt-get purge apparmor -y
apt-get update -yqq
apt-get upgrade -yqq
export DEBIAN_FRONTEND=noninteractive
apt-get install -qqy mysql-server mysql-server apache2 libapache2-mod-php5 libapache2-mod-bw php5-common php5-suhosin php5-cli php5-mysql php5-gd php5-mcrypt php5-curl php-pear php5-imap php5-xmlrpc php5-xsl db4.7-util zip webalizer build-essential bash-completion dovecot-mysql dovecot-imapd dovecot-pop3d dovecot-common dovecot-managesieved dovecot-lmtpd postfix postfix-mysql libsasl2-modules-sql libsasl2-modules proftpd-mod-mysql bind9 bind9utils

mkdir -p /var/zpanel/{hostdata,logs,backups,temp}
mkdir -p /var/zpanel/hostdata/zadmin/public_html
mkdir -p /var/zpanel/logs/proftpd
chmod -R 777 /var/zpanel/

chown -R www-data:www-data /var/zpanel/hostdata/

ln -s /etc/zpanel/panel/bin/zppy /usr/bin/zppy
ln -s /etc/zpanel/panel/bin/setso /usr/bin/setso
ln -s /etc/zpanel/panel/bin/setzadmin /usr/bin/setzadmin

chmod +x /etc/zpanel/panel/bin/zppy /etc/zpanel/panel/bin/setso

sed -i "s|YOUR_ROOT_MYSQL_PASSWORD|vagrant|" /etc/zpanel/panel/cnf/db.php
cc -o /etc/zpanel/panel/bin/zsudo /etc/zpanel/configs/bin/zsudo.c
chown root /etc/zpanel/panel/bin/zsudo
chmod +s /etc/zpanel/panel/bin/zsudo

service mysql start
mysqladmin -u root password vagrant
mysql -u root -pvagrant -e "CREATE SCHEMA zpanel_roundcube";
cat /etc/zpanel/configs/zpanelx-install/sql/*.sql | mysql -u root -pvagrant
mysql -u root -pvagrant -e "UPDATE mysql.user SET Password=PASSWORD('vagrant') WHERE User='postfix' AND Host='localhost';";
mysql -u root -pvagrant -e "FLUSH PRIVILEGES";
setzadmin --set "vagrant";
/etc/zpanel/panel/bin/setso --set zpanel_domain zpanel.local
/etc/zpanel/panel/bin/setso --set server_ip $(curl ifconfig.me/ip)
/etc/zpanel/panel/bin/setso --set apache_changed "true"

###############################
# Postfix specific installation tasks...
mkdir /var/zpanel/vmail
chmod -R 770 /var/zpanel/vmail
useradd -r -u 150 -g mail -d /var/zpanel/vmail -s /sbin/nologin -c "Virtual maildir" vmail
chown -R vmail:mail /var/zpanel/vmail
mkdir -p /var/spool/vacation
useradd -r -d /var/spool/vacation -s /sbin/nologin -c "Virtual vacation" vacation
chmod -R 770 /var/spool/vacation
ln -s /etc/zpanel/configs/postfix/vacation.pl /var/spool/vacation/vacation.pl
postmap /etc/postfix/transport
chown -R vacation:vacation /var/spool/vacation
if ! grep -q "127.0.0.1 autoreply.zpanel.local" /etc/hosts; then echo "127.0.0.1 autoreply.zpanel.local" >> /etc/hosts; fi
sed -i "s|myhostname = control.yourdomain.com|myhostname = zpanel.local|" /etc/postfix/main.cf
sed -i "s|mydomain   = control.yourdomain.com|mydomain   = zpanel.local|" /etc/postfix/main.cf
rm -rf /etc/postfix/main.cf /etc/postfix/master.cf
ln -s /etc/zpanel/configs/postfix/master.cf /etc/postfix/master.cf
ln -s /etc/zpanel/configs/postfix/main.cf /etc/postfix/main.cf
sed -i "s|password \= postfix|password \= vagrant|" /etc/zpanel/configs/postfix/mysql-relay_domains_maps.cf
sed -i "s|password \= postfix|password \= vagrant|" /etc/zpanel/configs/postfix/mysql-virtual_alias_maps.cf
sed -i "s|password \= postfix|password \= vagrant|" /etc/zpanel/configs/postfix/mysql-virtual_domains_maps.cf
sed -i "s|password \= postfix|password \= vagrant|" /etc/zpanel/configs/postfix/mysql-virtual_mailbox_limit_maps.cf
sed -i "s|password \= postfix|password \= vagrant|" /etc/zpanel/configs/postfix/mysql-virtual_mailbox_maps.cf
sed -i "s|\$db_password \= 'postfix';|\$db_password \= 'vagrant';|" /etc/zpanel/configs/postfix/vacation.conf

# Dovecot specific installation tasks (includes Sieve)
mkdir -p /var/zpanel/sieve
chown -R vmail:mail /var/zpanel/sieve
mkdir -p /var/lib/dovecot/sieve/
touch /var/lib/dovecot/sieve/default.sieve
ln -s /etc/zpanel/configs/dovecot2/globalfilter.sieve /var/zpanel/sieve/globalfilter.sieve
rm -rf /etc/dovecot/dovecot.conf
ln -s /etc/zpanel/configs/dovecot2/dovecot.conf /etc/dovecot/dovecot.conf
sed -i "s|postmaster_address = postmaster@your-domain.tld|postmaster_address = postmaster@$fqdn|" /etc/dovecot/dovecot.conf
sed -i "s|password=postfix|password=vagrant|" /etc/zpanel/configs/dovecot2/dovecot-dict-quota.conf
sed -i "s|password=postfix|password=vagrant|" /etc/zpanel/configs/dovecot2/dovecot-mysql.conf
touch /var/log/dovecot.log
touch /var/log/dovecot-info.log
touch /var/log/dovecot-debug.log
chown vmail:mail /var/log/dovecot*
chmod 660 /var/log/dovecot*

# ProFTPD specific installation tasks
groupadd -g 2001 ftpgroup
useradd -u 2001 -s /bin/false -d /bin/null -c "proftpd user" -g ftpgroup ftpuser
sed -i "s|#SQLConnectInfo  zpanel_proftpd@localhost root password_here|SQLConnectInfo   zpanel_proftpd@localhost root vagrant|" /etc/zpanel/configs/proftpd/proftpd-mysql.conf
rm -rf /etc/proftpd/proftpd.conf
touch /etc/proftpd/proftpd.conf
if ! grep -q "include /etc/zpanel/configs/proftpd/proftpd-mysql.conf" /etc/proftpd/proftpd.conf; then echo "include /etc/zpanel/configs/proftpd/proftpd-mysql.conf" >> /etc/proftpd/proftpd.conf; fi
chmod -R 644 /var/zpanel/logs/proftpd
serverhost=`hostname`

# Apache HTTPD specific installation tasks...
if ! grep -q "Include /etc/zpanel/configs/apache/httpd.conf" /etc/apache2/apache2.conf; then echo "Include /etc/zpanel/configs/apache/httpd.conf" >> /etc/apache2/apache2.conf; fi
sed -i 's|DocumentRoot "/var/www/html"|DocumentRoot "/etc/zpanel/panel"|' /etc/apache2/apache2.conf
sed -i 's|Include sites-enabled/||' /etc/apache2/apache2.conf
chown -R www-data:www-data /var/zpanel/temp/
if ! grep -q "127.0.0.1 "zpanel.local /etc/hosts; then echo "127.0.0.1 "zpanel.local >> /etc/hosts; fi
if ! grep -q "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" /etc/sudoers; then echo "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" >> /etc/sudoers; fi
a2enmod rewrite
service apache2 restart

# PHP specific installation tasks...
sed -i "s|;date.timezone =|date.timezone = $tz|" /etc/php5/cli/php.ini
sed -i "s|;date.timezone =|date.timezone = $tz|" /etc/php5/apache2/php.ini
sed -i "s|;upload_tmp_dir =|upload_tmp_dir = /var/zpanel/temp/|" /etc/php5/cli/php.ini
sed -i "s|;upload_tmp_dir =|upload_tmp_dir = /var/zpanel/temp/|" /etc/php5/apache2/php.ini

# Permissions fix for Apache and ProFTPD (to enable them to play nicely together!)
if ! grep -q "umask 002" /etc/apache2/envvars; then echo "umask 002" >> /etc/apache2/envvars; fi
if ! grep -q "127.0.0.1 $serverhost" /etc/hosts; then echo "127.0.0.1 $serverhost" >> /etc/hosts; fi
usermod -a -G www-data ftpuser
usermod -a -G ftpgroup www-data

# BIND specific installation tasks...
chmod -R 777 /etc/zpanel/configs/bind/zones/
mkdir /var/zpanel/logs/bind
mkdir -p /var/named/dynamic
touch /var/named/dynamic/managed-keys.bind
touch /var/zpanel/logs/bind/bind.log
chown root:root /etc/bind/rndc.key
chown -R bind:bind /var/named/
chmod 755 /etc/bind/rndc.key
chmod -R 777 /var/zpanel/logs/bind/bind.log
chmod -R 777 /etc/zpanel/configs/bind/etc
rm -rf /etc/bind/named.conf /etc/bind/rndc.conf /etc/bind/rndc.key
rndc-confgen -a
ln -s /etc/zpanel/configs/bind/named.conf /etc/bind/named.conf
ln -s /etc/zpanel/configs/bind/rndc.conf /etc/bind/rndc.conf
if ! grep -q "include \"/etc/zpanel/configs/bind/etc/log.conf\";" /etc/bind/named.conf; then echo "include \"/etc/zpanel/configs/bind/etc/log.conf\";" >> /etc/bind/named.conf; fi
ln -s /usr/sbin/named-checkconf /usr/bin/named-checkconf
ln -s /usr/sbin/named-checkzone /usr/bin/named-checkzone
ln -s /usr/sbin/named-compilezone /usr/bin/named-compilezone
cat /etc/bind/rndc.key | cat - /etc/bind/named.conf > /etc/bind/named.conf.new && mv /etc/bind/named.conf.new /etc/bind/named.conf
cat /etc/bind/rndc.key | cat - /etc/bind/rndc.conf > /etc/bind/rndc.conf.new && mv /etc/bind/rndc.conf.new /etc/bind/rndc.conf
rm -rf /etc/bind/rndc.key

# CRON specific installation tasks...
mkdir -p /var/spool/cron/crontabs/
mkdir -p /etc/cron.d/
touch /var/spool/cron/crontabs/www-data
touch /etc/cron.d/www-data
crontab -u www-data /var/spool/cron/crontabs/www-data
cp /etc/zpanel/configs/cron/zdaemon /etc/cron.d/zdaemon
chmod -R 644 /var/spool/cron/crontabs/
chmod -R 644 /etc/cron.d/
chown -R www-data:www-data /var/spool/cron/crontabs/

# Webalizer specific installation tasks...
rm -rf /etc/webalizer/webalizer.conf

# Roundcube specific installation tasks...
sed -i "s|YOUR_MYSQL_ROOT_PASSWORD|vagrant|" /etc/zpanel/configs/roundcube/db.inc.php
sed -i "s|#||" /etc/zpanel/configs/roundcube/db.inc.php
rm -rf /etc/zpanel/panel/etc/apps/webmail/config/main.inc.php
ln -s /etc/zpanel/configs/roundcube/main.inc.php /etc/zpanel/panel/etc/apps/webmail/config/main.inc.php
ln -s /etc/zpanel/configs/roundcube/config.inc.php /etc/zpanel/panel/etc/apps/webmail/plugins/managesieve/config.inc.php
ln -s /etc/zpanel/configs/roundcube/db.inc.php /etc/zpanel/panel/etc/apps/webmail/config/db.inc.php

# Enable system services and start/restart them as required.
service apache2 start
service postfix restart
service dovecot start
service cron reload
service mysql start
service bind9 start
service proftpd start
service atd start
php /etc/zpanel/panel/bin/daemon.php

touch /zplock
###
else
# problem with vagrant and start apache when there is
# an include , must be due to late folder mounting
service apache2 start
fi


