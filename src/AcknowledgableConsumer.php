<?php

namespace Brash\RabbitQueue;

use PhpAmqpLib\Message\AMQPMessage;

abstract class AcknowledgableConsumer
{
    protected function acknowledge(AMQPMessage $message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}
