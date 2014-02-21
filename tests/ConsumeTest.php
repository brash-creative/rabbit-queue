<?php
namespace RabbitQueueTest;

use RabbitQueue\Services\Message;

class ConsumeTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    public function testConsume()
    {
        $message    = new Message();
        $message->setQueue('test')->setPayload('test');

        $amqp       = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
                    ->disableOriginalConstructor()
                    ->getMock();
        $service    = \Mockery::mock('RabbitQueue\Services\Consume[getChannel]', array($amqp));
        $channel    = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
                    ->disableOriginalConstructor()
                    ->getMock();

        $channel->expects($this->once())
            ->method('basic_consume')
            ->will($this->returnValue((string) $message));

        $channel->expects($this->never())->method('wait');

        $service->shouldReceive('getChannel')->once()->andReturn($channel);

        $service->consume('test', 'test', 'test');
    }
}
