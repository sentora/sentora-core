# phpSysInfo
# VERSION       1

FROM ubuntu:14.04

MAINTAINER phpSysInfo

RUN apt-get install -y apache2 php5 git

RUN git clone https://github.com/phpsysinfo/phpsysinfo.git /var/www/html/phpsysinfo
RUN cp /var/www/html/phpsysinfo/phpsysinfo.ini.new /var/www/html/phpsysinfo/phpsysinfo.ini

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid

CMD ["/usr/sbin/apache2", "-D", "FOREGROUND"]

EXPOSE 80
