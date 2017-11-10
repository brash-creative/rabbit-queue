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

        if (true === empty($this->getQueue())) {
            throw new QueueException("No queue set");
        }

        $this->getChannel()->queue_declare(
            $this->getQueue(),
            false,
            true,
            false,
            false
        );
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
            $this->getChannel()->basic_publish($msg, $this->exchange, $this->getQueue());
        } catch (\Exception $e) {
            throw new QueueException("Could not push to queue", $e->getCode(), $e);
        }
    }

    /**
     * @param callable $consumer
     *
     * @throws QueueException
     */
    public function pull($consumer)
    {
        $consumer = $this->determineCallable($consumer);

        try {
            $this->getChannel()->basic_qos(0, 1, false);
            $this->getChannel()->basic_consume($this->getQueue(), '', false, false, false, false, $consumer);
        } catch (\Exception $e) {
            throw new QueueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param callable $consumer
     *
     * @throws QueueException
     */
    public function pullNoAck($consumer)
    {
        $consumer = $this->determineCallable($consumer);

        try {
            $this->getChannel()->basic_consume($this->getQueue(), '', false, true, false, false, $consumer);
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

    private function determineCallable($callable): callable
    {
        if (is_string($callable) && class_exists($callable)) {
            return new $callable;
        }

        if (!is_callable($callable)) {
            throw new QueueException("Consumer must be a callable");
        }

        return $callable;
    }

    abstract protected function getQueue(): string;
}
