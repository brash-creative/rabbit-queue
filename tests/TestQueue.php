<?php

namespace RabbitQueueTest;

use Brash\RabbitQueue\RabbitQueue;
use PhpAmqpLib\Connection\AMQPConnection;

class TestQueue extends RabbitQueue
{
    public function __construct(AMQPConnection $connection)
    {
        parent::__construct($connection);
    }
}
