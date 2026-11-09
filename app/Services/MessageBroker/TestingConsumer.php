<?php

namespace App\Services\MessageBroker;

use GlobalXtreme\RabbitMQ\Queue\Contract\GXRabbitMQConsumerContract;

class TestingConsumer implements GXRabbitMQConsumerContract
{
    public static function consume(array|string $data)
    {
    }
}
