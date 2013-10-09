<?php

use RabbitQueue\Services\Message;

class MessageTest extends PHPUnit_Framework_TestCase {
    public function testStringException()
    {
        $this->setExpectedException('RabbitQueue\Exceptions\MessageException');

        $message    = new Message();
        $message->setPayload(array('test'=>1));
    }
}