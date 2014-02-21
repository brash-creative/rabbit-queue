<?php

namespace RabbitQueue\Abstracts;

use PhpAmqpLib\Connection\AMQPConnection;

class QueueAbstract
{
    /**
     * @var AMQPConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $channel;

    public function __construct(AMQPConnection $connection)
    {
        $this->setConnection($connection);
    }

    /**
     * @param AMQPConnection $connection
     *
     * @return AMQPConnection
     */
    public function setConnection(AMQPConnection $connection)
    {
        $this->connection = $connection;
        return $this->connection;
    }

    /**
     * @return AMQPConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $channel
     *
     * @return string
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this->channel;
    }

    /**
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    public function getChannel()
    {
        if (!$this->channel) {
            $this->setChannel($this->getConnection()->channel());
        }
        return $this->channel;
    }
}
