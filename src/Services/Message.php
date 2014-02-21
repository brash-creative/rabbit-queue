<?php

namespace RabbitQueue\Services;

use RabbitQueue\Interfaces\MessageInterface;
use RabbitQueue\Exceptions\MessageException;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $queue;

    /**
     * @var string
     */
    protected $payload;

    /**
     * @param $queueName
     * @return mixed
     */
    public function setQueue($queueName)
    {
        $this->queue = $queueName;
        return $this->queue;
    }

    /**
     * @return mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set payload as JSON string or serialized object
     * @param $payload
     *
     * @return string
     * @throws \RabbitQueue\Exceptions\MessageException
     */
    public function setPayload($payload)
    {
        if (!is_string($payload)) {
            throw new MessageException("Message payload must be a string or serialized object");
        }

        $this->payload  = $payload;
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getPayload();
    }
}
