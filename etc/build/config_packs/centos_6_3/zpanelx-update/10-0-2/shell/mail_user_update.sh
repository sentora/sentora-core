#!/bin/bash

#Stop Services
service postfix stop
service dovecot stop

#change UID
usermod -u 101 vmail

#Start services
service dovecot start
service postfix start
