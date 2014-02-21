<?php
namespace RabbitQueueTest;

use RabbitQueue\Services\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testStringException()
    {
        $this->setExpectedException('RabbitQueue\Exceptions\MessageException');

        $message    = new Message();
        $message->setPayload(array('test'=>1));
    }

    public function testIsString()
    {
        $message    = new Message();
        $message->setPayload('test');

        $this->assertTrue(is_string((string) $message));
    }
}
