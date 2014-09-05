[![Build Status](https://travis-ci.org/paul-crashley/rabbit-queue.png?branch=develop)](https://travis-ci.org/paul-crashley/rabbit-queue)

# Basic RabbitMQ interface

A basic package to publish and/or consume AMQP messages via RabbitMQ

First, you need to create your queue class, which extends the Rabbit Brash\RabbitQueue\RabbitQueue class
with the queue parameter set with your desired queue name.

```php
<?php
use Brash\RabbitQueue\RabbitQueue

class MyQueue extends RabbitQueue {

    protected $queue = 'EXAMPLE_QUEUE';

    public function __construct(AMQPConnection $connection)
    {
        parent::__construct($connection)
    }
}
```

This class can then be used to push/pull all messages to that queue.

## Usage Example - Publish

```php
<?php
use PhpAmqpLib\Connection\AMQPConnection;
use Brash\RabbitQueue\QueueException;

try {
    // AMQPConnection(host, port, username, password)
    $message    = "This is my message";
    $amqp       = new AMQPConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $publish    = new MyQueue($amqp);
    $publish->push($message);
} catch (QueueException $e) {
    // Catch publish errors
} catch (\Exception $e) {
    // Catch all other errors
}
```

The consumer will retrieve messages from the queue and pass them to an object/method defined by the user for processing.

You can pull messages down with or without acknowledgement.

## Usage Example - Consume (acknowledgement)

```php
<?php
use PhpAmqpLib\Connection\AMQPConnection;
use Brash\RabbitQueue\QueueException;

// A class containing a method that the consumer can send the retrieved message body
try {
    $amqp           = new AMQPConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $processObject  = new ExampleProcessClass();
    $consume        = new MyQueue($amqp);
    $consume->pull($processObject, 'exampleProcessMethod');

    // Keep listening to the queue...
    $consume->poll();
} catch (QueueException $e) {
    // Catch consume errors
} catch (\Exception $e) {
    // Catch all other errors
}
```

When using this method, you will have to send the acknowledgement after the message has been processed in the method you defined.

In this example...

```php
<?php
use PhpAmqpLib\Message\AMQPMessage;

class ExampleProcessClass {
    public function exampleProcessMethod(AMQPMessage $message)
    {
        $body = $message->body;

        // Do something with the message

        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}
```

## Usage Example - Consume (no acknowledgement)

```php
<?php
use PhpAmqpLib\Connection\AMQPConnection;
use Brash\RabbitQueue\QueueException;

// A class containing a method that the consumer can send the retrieved message body
try {
    $amqp           = new AMQPConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $processObject  = new ExampleProcessClass();
    $consume        = new MyQueue($amqp);
    $consume->pullNoAck($processObject, 'exampleProcessMethod');

    // Keep listening to the queue...
    $consume->poll();
} catch (QueueException $e) {
    // Catch consume errors
} catch (\Exception $e) {
    // Catch all other errors
}
```
