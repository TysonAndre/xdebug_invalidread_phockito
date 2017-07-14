FROM ubuntu:17.04
RUN apt-get update -y
RUN apt-get install -y python-software-properties
RUN apt-get install -y software-properties-common
ENV LANG=C.UTF-8

RUN add-apt-repository -y ppa:ondrej/php
RUN apt-get update -y
# 7.1.7.retag-1+ubuntu17.04.1+deb.sury.org+1
RUN apt-get install -y php7.1=7.1.7.retag-1+ubuntu17.04.1+deb.sury.org+1 php7.1-dev=7.1.7.retag-1+ubuntu17.04.1+deb.sury.org+1
RUN apt-get install -y valgrind
RUN curl -sSL https://xdebug.org/files/xdebug-2.5.5.tgz > xdebug-2.5.5.tgz
RUN tar xf xdebug-2.5.5.tgz
RUN cd xdebug-2.5.5 && phpize && ./configure && make install
ADD invalid_read_test.php .
ENV USE_ZEND_ALLOC 0
ENTRYPOINT valgrind php --no-php-ini -d zend_extension=xdebug.so -d xdebug.collect_params=3 invalid_read_test.php
