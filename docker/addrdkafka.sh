#!/usr/bin/env bash

#-------------------------
# ubuntu - github actions
# fedora - dev environment
#-------------------------

OSNAME=$(source /etc/os-release | echo $ID| grep fedora)

if [ -z $OSNAME ]
    then
        export PKMAN="apt"
        export LISTS="/var/lib/apt/lists/*"
fi

if [ -n $OSNAME ]
    then
        export PKMAN="dnf"
        export LISTS="/var/lib/dnf/lists/*"
fi

pwd

sudo $PKMAN -y update  \
&& $PKMAN -y install curl wget zip unzip git \
&& rm -rf /var/lib/apt/lists/*

$PKMAN install python3-pip

sudo git clone --depth 1 https://github.com/edenhill/librdkafka.git \
    && ( \
        cd librdkafka \
        && ./configure \
        && make \
        && make install \
    ) \
    && $PKMAN install librdkafka-dev \
    && pecl install rdkafka \
    && echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini

