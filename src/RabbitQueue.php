<?php

namespace Brash\RabbitQueue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

abstract class RabbitQueue
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection
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
     * @param AMQPStreamConnection    $AMQPStreamConnection
     *
     * @throws QueueException
     */
    public function __construct(AMQPStreamConnection $AMQPStreamConnection)
    {
        $this->connection = $AMQPStreamConnection;

        if (true === empty($this->queue)) {
            throw new QueueException("No queue set");
        }

        $this->getChannel()->queue_declare($this->queue);
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
     * @param callable $consumer
     *
     * @throws QueueException
     */
    public function pull(callable $consumer)
    {
        try {
            $this->getChannel()->basic_qos(0, 1, false);
            $this->getChannel()->basic_consume($this->queue, '', false, false, false, false, $consumer);
        } catch (\Exception $e) {
            throw new QueueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param callable $consumer
     *
     * @throws QueueException
     */
    public function pullNoAck(callable $consumer)
    {
        try {
            $this->getChannel()->basic_consume($this->queue, '', false, true, false, false, $consumer);
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
