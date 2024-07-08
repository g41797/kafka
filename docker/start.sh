#!/usr/bin/env bash

export IMAGE=apache/kafka:latest

if ! [ -f docker/docker-compose.yml ]; then
  export DOCOMPOSE=./docker-compose.yml
  export KAFKACTL=../bin/kafkactl
else
  export DOCOMPOSE=docker/docker-compose.yml
  export KAFKACTL=bin/kafkactl
fi

docker compose -f $DOCOMPOSE up -d

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
echo ''

date
sudo netstat --tcp --listening --programs --numeric|grep 9092

date

# $KAFKACTL config add "my-context" --broker localhost:9092
# $KAFKACTL get brokers

