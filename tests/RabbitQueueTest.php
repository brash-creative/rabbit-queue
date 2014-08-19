<?php
namespace RabbitQueueTest;

include 'TestQueue.php';

class RabbitQueueTest extends \PHPUnit_Framework_TestCase
{
    public function testQueueException()
    {
        $this->setExpectedException('Brash\RabbitQueue\QueueException');

        $amqp       = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
           ->disableOriginalConstructor()
           ->getMock();

        $channel    = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();


        $class = new TestQueue($amqp, $channel);
    }
}
