#!/usr/bin/env bash

pwd

export IMAGE=apache/kafka:latest

tail docker/docker-compose.yml

docker compose -f docker/docker-compose.yml up -d

echo 'Waiting for open port 9092'

for (( ; ; ))
do
    sudo netstat --tcp --listening --programs --numeric|grep -o 9092|wc -l >/tmp/openedports
    OPENED=$(< /tmp/openedports)
    if [ $OPENED -gt 0 ];
    then
        break
    fi

    echo -n .
    sleep 1
done


echo 'ok'

