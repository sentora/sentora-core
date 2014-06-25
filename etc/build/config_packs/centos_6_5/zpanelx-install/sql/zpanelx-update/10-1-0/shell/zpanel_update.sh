chmod -R 744 /var/spool/cron/

##PASSWORD GEN##
genpasswd() {
     l=$1
           [ "$l" == "" ] && l=16
          tr -dc A-Za-z0-9 < /dev/urandom | head -c ${l} | xargs
}

## Determine if we need to update the postfix user
result=`mysql -u postfix -ppostfix --skip-column-names -e "SHOW DATABASES LIKE 'zpanel_postfix'"`

if [ "$result" == "zpanel_postfix" ]; then

password=`genpasswd`;
mysqlrootpass=`cat /root/mysqlrootpass`
echo "UPDATE mysql.user SET Password=PASSWORD('$password') WHERE User='postfix' AND Host='localhost';" | mysql -u root -p$mysqlrootpass
echo "FLUSH PRIVILEGES;" | mysql -u root -p$mysqlrootpass

sed -i "s|password \= postfix|password \= $password|" /etc/zpanel/configs/postfix/mysql-relay_domains_maps.cf
sed -i "s|password \= postfix|password \= $password|" /etc/zpanel/configs/postfix/mysql-virtual_alias_maps.cf
sed -i "s|password \= postfix|password \= $password|" /etc/zpanel/configs/postfix/mysql-virtual_domains_maps.cf
sed -i "s|password \= postfix|password \= $password|" /etc/zpanel/configs/postfix/mysql-virtual_mailbox_limit_maps.cf
sed -i "s|password \= postfix|password \= $password|" /etc/zpanel/configs/postfix/mysql-virtual_mailbox_maps.cf
sed -i "s|\$db_password \= 'postfix';|\$db_password \= '$password';|" /etc/zpanel/configs/postfix/vacation.conf
sed -i "s|password=postfix|password=$password|" /etc/zpanel/configs/dovecot2/dovecot-dict-quota.conf
sed -i "s|password=postfix|password=$password|" /etc/zpanel/configs/dovecot2/dovecot-mysql.conf
echo -e "Your new MySQL 'postfix' is : $password";
echo -e "MySQL Postfix Password : $password" >> /root/passwords.txt
fi

rm /etc/zpanel/panel/modules/backupmgr/code/getdownload.php
