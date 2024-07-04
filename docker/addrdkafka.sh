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

sudo apt -y update  \
&& apt -y install curl wget zip unzip git \
&& rm -rf /var/lib/apt/lists/*

apt -y install python3-pip

sudo git clone --depth 1 https://github.com/edenhill/librdkafka.git \
    && ( \
        cd librdkafka \
        && sudo ./configure \
        && sudo make \
        && sudo make install \
    ) \
    && sudo apt install librdkafka-dev \
    && sudo pecl install rdkafka \
    && echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini

