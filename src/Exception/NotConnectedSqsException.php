<?php

declare(strict_types=1);

namespace G41797\Queue\Kafka\Exception;

use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class NotConnectedSqsException extends \RuntimeException implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Not connected to SQS.';
    }

    public function getSolution(): ?string
    {
        return 'Check your SQS configuration.';
    }
}

