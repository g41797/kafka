<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka\Functional;

use G41797\Queue\Kafka\Configuration;

use longlang\phpkafka\Consumer\Consumer;

class SnippetsTest extends FunctionalTestCase
{
    public function testPurgeTopic(): void
    {

    }


    static public function purgeTopic(): bool
    {
        try {
            $consumer = new Consumer(Configuration::defaultConsumerConfig());
        }
        catch (\Throwable $exception) {
            return false;
        }

        while (true) {
            $message = $consumer->consume();
            if ($message) {
                var_dump($message->getKey() . ':' . $message->getValue());
                $consumer->ack($message); // ack
            } else {
                break;
            }
        }

        $consumer->close();

        return true;
    }
}
