#!/bin/bash

#Stop Services
service postfix stop
service dovecot stop

#change UID
usermod -u 101 vmail

#Start services
service dovecot start
service postfix start


#Postfix configuration fix
fqdn=`/bin/hostname`
rm -rf /etc/postfix/main.cf /etc/postfix/master.cf
ln -s /etc/zpanel/configs/postfix/master.cf /etc/postfix/master.cf
ln -s /etc/zpanel/configs/postfix/main.cf /etc/postfix/main.cf
sed -i "s|myhostname = control.yourdomain.com|myhostname = $fqdn|" /etc/postfix/main.cf
sed -i "s|mydomain = control.yourdomain.com|mydomain = $fqdn|" /etc/postfix/main.cf
