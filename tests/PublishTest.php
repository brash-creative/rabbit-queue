<?php
namespace RabbitQueueTest;

use RabbitQueue\Services\Message;

class PublishTest extends \PHPUnit_Framework_TestCase
{
    public function testNoQueueException()
    {
        $this->setExpectedException('RabbitQueue\Exceptions\PublishException');

        $amqp       = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
                           ->disableOriginalConstructor()
                           ->getMock();
        $service    = \Mockery::mock('RabbitQueue\Services\Publish[getChannel]', array($amqp));
        $channel    = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
                           ->disableOriginalConstructor()
                           ->getMock();

        $channel->expects($this->never())
                ->method('basic_publish')
                ->will($this->returnValue(true));

        $message    = new Message();

        $service->shouldReceive('getChannel')->never();

        $service->publish($message);
    }

    public function testPublish()
    {
        $message    = new Message();
        $message->setQueue('test')->setPayload('test');

        $amqp       = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
                           ->disableOriginalConstructor()
                           ->getMock();
        $service    = \Mockery::mock('RabbitQueue\Services\Publish[getChannel]', array($amqp));
        $channel    = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
                           ->disableOriginalConstructor()
                           ->getMock();

        $channel->expects($this->once())
                ->method('basic_publish')
                ->will($this->returnValue(true));

        $service->shouldReceive('getChannel')->once()->andReturn($channel);

        $service->publish($message);
    }
}
