# Yii3 Queue Adapter for Apache Kafka 


[![build](https://github.com/g41797/queue-kafka/actions/workflows/tests.yml/badge.svg)](https://github.com/g41797/queue-kafka/actions/workflows/tests.yml)

## Description

Yii3 Queue Adapter for [**Apache Kafka**](https://kafka.apache.org/) is adapter in [Yii3 Queue Adapters family.](https://github.com/yiisoft/queue/blob/master/docs/guide/en/adapter-list.md)
    
Implementation of adapter is based on synchronous mode of [Swoole PHP Kafka client](https://github.com/swoole/phpkafka) library.

## Requirements

- PHP 8.2 or higher.

## Installation

The package could be installed with composer:

```shell
composer require g41797/queue-kafka
```

## General usage

- As part of [Yii3 Queue Framework](https://github.com/yiisoft/queue/blob/master/docs/guide/en/README.md)
- Stand-alone


## Configuration

Default configuration:
```php
[
     'bootstrapServers' => 'localhost:9092',  // Format `'127.0.0.1:9092,127.0.0.1:9093'` or `['127.0.0.1:9092','127.0.0.1:9093']`
]
```

## Limitations

### Job Status

  [Job Status](https://github.com/yiisoft/queue/blob/master/docs/guide/en/usage.md#job-status)
```php
// Push a job into the queue and get a message ID.
$id = $queue->push(new SomeJob());

// Get job status.
$status = $queue->status($id);
```
is not supported.

### Testing

Unit-testing is supported for local environment.
Run of phpunit under GitHib action was disabled
because problem of kafka configuration.
Description of cumbersome configuration see [Kafka Listeners - Explained](https://rmoff.net/2018/08/02/kafka-listeners-explained/)


## License

Yii3 Queue Adapter for Apache Kafka is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.
