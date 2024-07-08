<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka\Functional;

use G41797\Queue\Kafka\Broker;

class CleanerTest extends FunctionalTestCase
{
    public function testPurgeTopic(): void
    {

    }


    static public function purgeTopic(): bool
    {
        try {
            $broker = new Broker();

            while (true) {
                $job = $broker->pull(2.0);
                if (null == $job){
                    return true;
                }
            }
        }
        catch (\Throwable $exception) {
            return false;
        }

        return true;
    }
}
