#!/bin/bash

##PASSWORD GEN##
genpasswd() {
     l=$1
           [ "$l" == "" ] && l=16
          tr -dc A-Za-z0-9 < /dev/urandom | head -c ${l} | xargs
}

##SET PASSWORD##
zadminNewPass=`genpasswd`
setzadmin --set $zadminNewPass

##BUILD RETURN MESSAGE##
updatemessages=""
updatemessages="$updatemessages zadmin password has been updated to : $zadminNewPass\n"
updatemessages="$updatemessages zadmin api hash has been updated to a random hash.\n"

##STORE AND RETURN##
touch /root/passwords.txt
echo "zadmin Password :: $zadminNewPass" >> /root/passwords.txt
echo -e $updatemessages

