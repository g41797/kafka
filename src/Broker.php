<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Ramsey\Uuid\Uuid;

use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumeMessage;
use longlang\phpkafka\Producer\Producer;


use Yiisoft\Queue\Enum\JobStatus;
use Yiisoft\Queue\Message\IdEnvelope;
use Yiisoft\Queue\Message\JsonMessageSerializer;
use Yiisoft\Queue\Message\MessageInterface;

use G41797\Queue\Kafka\Configuration as BrokerConfiguration;

use G41797\Queue\Kafka\Exception\NotSupportedStatusMethodException;
use G41797\Queue\Kafka\Exception\NotConnectedKafkaException;


class Broker implements BrokerInterface
{
    public const SUBSCRIPTION_NAME = 'jobs';

    private JsonMessageSerializer $serializer;

    public function __construct(
        private string                  $channelName    = Adapter::DEFAULT_CHANNEL_NAME,
        public  ?BrokerConfiguration    $configuration  = null,
        public  ?LoggerInterface        $logger         = null
    ) {
        $this->serializer = new JsonMessageSerializer();

        if (null == $configuration) {
            $this->configuration = new BrokerConfiguration();
        }

        if (null == $logger) {
            $this->logger = new NullLogger();
        }

        return;
    }

    static public function default(): Broker
    {
        return new Broker();
    }

    public function withChannel(string $channel): BrokerInterface
    {
        if ($channel == $this->channelName) {
            return $this;
        }

        return new self($channel, $this->configuration, $this->logger);
    }

    private ?Producer $producer = null;
    public function push(MessageInterface $job): ?IdEnvelope
    {
        try {
            if ($this->producer == null) {
                $producer = new Producer($this->configuration->forProducer());
                $this->producer = $producer;
            }
        }
        catch (\Throwable ) {
            throw new NotConnectedKafkaException();
        }

        $env = $this->submit($job);

        if ($env == null)
        {
            $this->producer->close();
            $this->producer = null;
        }

        return $env;
    }

    private function submit(MessageInterface $job): ?IdEnvelope
    {
        try {
            $jobId      = Uuid::uuid7()->toString();
            $payload    = $this->serializer->serialize($job);

            $this->producer->send($this->channelName, $payload, $jobId);

            return new IdEnvelope($job, $jobId);
        }
        catch (\Throwable ) {
            return null;
        }
    }

    public function jobStatus(string $id): ?JobStatus
    {
        throw new NotSupportedStatusMethodException();
    }

    private ?Consumer $receiver = null;

    public function pull(float $timeout): ?IdEnvelope
    {
        try {
            if ($this->receiver == null) {
                $consumer = new Consumer($this->configuration->forConsumer($this->channelName));
                $this->receiver = $consumer;
            }
        }
        catch (\Throwable ) {
            throw new NotConnectedKafkaException();
        }

        try
        {
            $kafkaMsg = $this->next($timeout);

            if (null == $kafkaMsg) { return null;}

            $job    = $this->serializer->unserialize($kafkaMsg->getValue());
            $jid    = $kafkaMsg->getKey();

            $this->receiver->ack($kafkaMsg);

            return new IdEnvelope($job, $jid);
        }
        catch (\Exception $exc) {
            $this->receiver->close();
            $this->receiver = null;
            return null;
        }
    }

    public function next(float $timeout = 0): ?ConsumeMessage
    {
        $start = microtime(true);

        while (true) {
            $message = $this->receiver->consume();
            if (null !== $message) {
                return $message;
            }
            if ($timeout && ($start + $timeout < microtime(true))) {
                continue;
            }
            return null;
        }
    }

    public function clean(): int
    {
        $count = 0;

        while (true)
        {
            $recv = $this->pull(1.0);
            if ($recv == null)
            {
                break;
            }

            $count += 1;
        }

        return $count;
    }

    public function done(string $id): bool
    {
        return !empty($id);
    }

}
