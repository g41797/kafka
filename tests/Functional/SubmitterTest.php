<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka\Functional;

use G41797\Queue\Kafka\Adapter;
use G41797\Queue\Kafka\Broker;
use G41797\Queue\Kafka\Configuration;

class SubmitterTest extends FunctionalTestCase
{
    public function testSubmit(): void
    {
        $count = 10;

        $this->assertEquals($count, count($this->submitJobs($count)));

        $this->assertEquals($count, self::testBroker()->clean());
    }

    private function submitJobs(int $count): array
    {
        $submitted = [];
        $submitter = self::testBroker();

        for ($i = 0; $i < $count; $i++) {
            $job = self::defaultJob();
            $env = $submitter->push($job);
            if ($env == null) {
                break;
            }
            $submitted[] = $env;
        }
        return $submitted;
    }

    static public function testBroker(): Broker
    {
        return new Broker();
    }

}
