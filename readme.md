[![Build Status](https://travis-ci.org/brash-creative/rabbit-queue.svg?branch=master)](https://travis-ci.org/brash-creative/rabbit-queue)

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
    
    public function getQueue(): string 
    {
        return $this->queue;
    }
}
```

This class can then be used to push/pull all messages to that queue.

## Usage Example - Publish

```php
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Brash\RabbitQueue\QueueException;

try {
    // AMQPConnection(host, port, username, password)
    $message    = "This is my message";
    $amqp       = new AMQPStreamConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $publish    = new MyQueue($amqp);
    $publish->push($message);
} catch (QueueException $e) {
    // Catch publish errors
} catch (\Exception $e) {
    // Catch all other errors
}
```

The consumer will retrieve messages from the queue and pass them to the chosen processor.

Processor methods involve passing a callable method, object/method pair or class path constant of an invokable class, e.g.

```php
$consume->pull([$class, 'method']);
$consume->pull(function (AMQPMessage $message){
    // Some code
})
$consume->pull(ExampleConsumer::class);
```

In the final example, ExampleConsumer class would look like:

For example:
```php
class ExampleConsumer
{
    public function __invoke(AMPQMessage $message)
    {
        // Code
    }   
}
```

You can also choose to pull messages down with or without acknowledgement.

## Usage Example - Consume (acknowledgement)

```php
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Brash\RabbitQueue\QueueException;

// A class containing a method that the consumer can send the retrieved message body
try {
    $amqp           = new AMQPStreamConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $consume        = new MyQueue($amqp);
    $consume->pull(ExampleConsumer::class);

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

class ExampleConsumer {
    public function __invoke(AMQPMessage $message)
    {
        $body = $message->body;

        // Do something with the message

        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }
}
```

Alternatively, you can extend the included AcknowledgableConsumer abstract class and call the `acknowledge` method:

```php
<?php
use PhpAmqpLib\Message\AMQPMessage;
use Brash\RabbitQueue\AcknowledgableConsumer;

class ExampleConsumer extends AcknowledgableConsumer
{
    public function __invoke(AMQPMessage $message)
    {
        $body = $message->body;

        // Do something with the message

        $this->acknowledge($message);
    }
}
```

## Usage Example - Consume (no acknowledgement)

```php
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Brash\RabbitQueue\QueueException;

// A class containing a method that the consumer can send the retrieved message body
try {
    $amqp           = new AMQPStreamConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $consume        = new MyQueue($amqp);
    $consume->pullNoAck(ExampleConsumer::class);

    // Keep listening to the queue...
    $consume->poll();
} catch (QueueException $e) {
    // Catch consume errors
} catch (\Exception $e) {
    // Catch all other errors
}
```
