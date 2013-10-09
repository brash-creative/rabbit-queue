<?php

namespace RabbitQueue\Services;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use RabbitQueue\Abstracts\QueueAbstract;
use RabbitQueue\Interfaces\MessageInterface;
use RabbitQueue\Exceptions\PublishException;

class Publish extends QueueAbstract {
    public function __construct(AMQPConnection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * @param MessageInterface $message
     *
     * @throws \RabbitQueue\Exceptions\PublishException
     */
    public function publish(MessageInterface $message)
    {
        if(!$message->getQueue())
            throw new PublishException("Message must have a queue set");

        try {
            $data       = new AMQPMessage((string) $message);
            $channel    = $this->getChannel();
            $channel->basic_publish($data, "", $message->getQueue());
        } catch (\Exception $e) {
            throw new PublishException($e->getMessage(), $e->getCode());
        }
    }
}