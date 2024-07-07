#!/usr/bin/env bash

docker run -d -e xeotek_kadeck_free="g41797@gmail.com" -e xeotek_kadeck_port=80 -p 80:80 --name kadeck -v kadeck_data:/root/.kadeck/ xeotek/kadeck:5.2.2

echo 'Waiting for open port 80'

for (( ; ; ))
do
    sudo netstat --tcp --listening --programs --numeric|grep -o 80|wc -l >/tmp/openedports
    OPENED=$(< /tmp/openedports)
    if [ $OPENED -gt 0 ];
    then
        break
    fi

    echo -n .
    sleep 1
done


echo 'ok'

