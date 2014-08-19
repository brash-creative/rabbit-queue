<?php

namespace Brash\RabbitQueue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RabbitQueue
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    protected $channel;

    /**
     * @var string
     */
    protected $exchange = "";

    /**
     * @param AMQPConnection    $amqpConnection
     *
     * @throws QueueException
     */
    public function __construct(AMQPConnection $amqpConnection)
    {
        $this->connection       = $amqpConnection;

        if (true === empty($this->queue)) {
            throw new QueueException("No queue set");
        }
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel()
    {
        if (null === $this->channel) {
            $this->channel  = new AMQPChannel($this->connection);
        }
        return $this->channel;
    }

    /**
     * @param string $exchange
     *
     * @return $this
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
        return $this;
    }

    /**
     * @return string
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param      $payload
     *
     * @throws \Brash\RabbitQueue\QueueException
     */
    public function push($payload)
    {
        $msg    = new AMQPMessage($payload);

        try {
            $this->getChannel()->basic_publish($msg, $this->exchange, $this->queue);
        } catch (\Exception $e) {
            throw new QueueException("Could not push to queue", $e->getCode(), $e);
        }
    }

    /**
     * @param $object
     * @param $method
     *
     * @throws \Brash\RabbitQueue\QueueException
     */
    public function pull($object, $method)
    {
        try {
            $this->getChannel()->queue_declare($this->queue);
            $this->getChannel()->basic_qos(0, 1, false);
            $this->getChannel()->basic_consume($this->queue, '', false, false, false, false, array($object, $method));
        } catch (\Exception $e) {
            throw new QueueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $object
     * @param $method
     *
     * @throws \Brash\RabbitQueue\QueueException
     */
    public function pullNoAck($object, $method)
    {
        try {
            $this->getChannel()->basic_consume($this->queue, '', false, true, false, false, array($object, $method));
        } catch (\Exception $e) {
            throw new QueueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function poll()
    {
        while (count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
    }
}
