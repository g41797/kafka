<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka;

use longlang\phpkafka\Consumer\ConsumerConfig as KafkaConsumerConfig;
use longlang\phpkafka\Producer\ProducerConfig as KafkaProducerConfig;

final class Configuration
{
    public readonly string $defaultGroupId;
    private array $config = [];

    public function __construct(
        array $config = []
    ) {
        $this->config = array_replace(self::default(), $config);
        $this->defaultGroupId = 'workers';
    }

    public function update(array $config): self
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

    public function forConsumer(string $topic = Adapter::DEFAULT_CHANNEL_NAME): KafkaConsumerConfig
    {
        $config = new KafkaConsumerConfig($this->config);
        $config->setGroupId($this->defaultGroupId);
        $config->setTopic($topic);
        return $config;
    }
    public function forProducer(): KafkaProducerConfig
    {
        return new KafkaProducerConfig($this->config);
    }

    static public function default(): array
    {
        return [
            'brokers' => '127.0.0.1:9092',
        ];
    }

    static public function defaultConsumerConfig(): KafkaConsumerConfig
    {
        return (new Configuration())->forConsumer();
    }

    static public function defaultProducerConfig(): KafkaProducerConfig
    {
        return (new Configuration())->forProducer();
    }

}
