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
&& sudo apt -y install curl wget zip unzip git \
&& sudo rm -rf /var/lib/apt/lists/*

INI_LOC=$(php -i|sed -n '/^Loaded Configuration File => /{s:^.*> ::;p;q}')
if [ -f "$INI_LOC" ]; then
  FOUND=$(sed -n 's:^[\t ]*::;/^;/d;/^$/d;s:[\t ]*=[\t ]*:=:;/extension="*imagick.so"*/{p;q}' <"$INI_LOC")
  if [ -z "$FOUND" ]; then
    sed -i~ '/^[\t ]*\[PHP\][\t ]*$/{s:$:\nextension=imagick.so:}' "$INI_LOC"
  fi
fi

echo $INI_LOC

tail $INI_LOC

sudo apt -y install python3-pip

sudo git clone --depth 1 https://github.com/edenhill/librdkafka.git \
    && ( \
        cd librdkafka \
        && sudo ./configure \
        && sudo make \
        && sudo make install \
    ) \
    && sudo pecl install rdkafka \
    && echo "extension=rdkafka.so" >> $INI_LOC \
    && echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini

