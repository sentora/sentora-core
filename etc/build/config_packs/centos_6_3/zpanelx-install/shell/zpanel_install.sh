#!/bin/bash

##PASSWORD GEN##
genpasswd() {
    	 l=$1
           [ "$l" == "" ] && l=16
          tr -dc A-Za-z0-9 < /dev/urandom | head -c ${l} | xargs
}

## SET PASSWORD##
zadminNewPass=`genpasswd`
setzadmin --set $zadminNewPass

##STORE AND RETURN##
touch /root/passwords.txt
echo "zadmin Password :: $zadminNewPass" >> /root/passwords.txt
echo $zadminNewPass
