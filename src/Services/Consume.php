<?php

namespace RabbitQueue\Services;

use PhpAmqpLib\Connection\AMQPConnection;
use RabbitQueue\Abstracts\QueueAbstract;
use RabbitQueue\Exceptions\ConsumeException;

class Consume extends QueueAbstract
{
    public function __construct(AMQPConnection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * Long running process that retrieves messages from the queue retrieves an AMQPMessage object and then sends it
     * to the object/method defined
     *
     * @param string $queue
     * @param object $object
     * @param string $method
     *
     * @throws \RabbitQueue\Exceptions\ConsumeException
     */
    public function consume($queue, $object, $method)
    {
        try {
            $channel    = $this->getChannel();
            $channel->basic_consume($queue, '', false, true, false, false, array($object, $method));

            echo 'Polling for messages...' . PHP_EOL;

            while (count($channel->callbacks)) {
                $channel->wait();
            }
        } catch (\Exception $e) {
            throw new ConsumeException($e->getMessage(), $e->getCode());
        }
    }
}
