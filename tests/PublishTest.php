<?php
use RabbitQueue\Services\Message;
use PhpAmqpLib\Connection\AMQPConnection;
use RabbitQueue\Services\Publish;

class PublishTest extends PHPUnit_Framework_TestCase {
    public function testNoQueueException()
    {
        $this->setExpectedException('RabbitQueue\Exceptions\PublishException');

        $message    = new Message();
        $amqp       = new AMQPConnection('rabbitmq-test.server.traveljigsaw.com', 5672, 'guest', 'guest');
        $publish    = new Publish($amqp);
        $publish->publish($message);
    }
}