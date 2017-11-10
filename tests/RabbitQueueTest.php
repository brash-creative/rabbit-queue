<?php
namespace Brash\RabbitQueue\Tests;

use Brash\RabbitQueue\QueueException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\TestCase;


class RabbitQueueTest extends TestCase
{
    public function testQueueException()
    {
        $this->expectException(QueueException::class);

        $amqp       = $this->getMockBuilder(AMQPStreamConnection::class)
           ->disableOriginalConstructor()
           ->getMock();

        $class = new TestQueue($amqp);
    }
}
