<?php

namespace RabbitQueueTest;

use Brash\RabbitQueue\RabbitQueue;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class TestQueue extends RabbitQueue
{
    public function __construct(AMQPStreamConnection $connection)
    {
        parent::__construct($connection);
    }
}
