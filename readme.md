# Basic RabbitMQ interface

A basic package to publish and/or consume AMQP messages via RabbitMQ

## Usage Example - Publish

Publish a JSON string or serialized object to the queue using the MessageInterface, which is then 
sent to the publish method.

```php
<?php
use PhpAmqpLib\Connection\AMQPConnection;
use RabbitQueue\Services\Message;
use RabbitQueue\Services\Publish;
use RabbitQueue\Exceptions\PublishException;

$message    = new Message();
$message->setQueue('EXAMPLE_QUEUE');
$message->setPayload('{"msg":"My test payload"}');

// New AMQPConnection from AMQPLib package,
// AMQPConnection(host, port, username, password)
try {
    $amqp       = new AMQPConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $publish    = new Publish($amqp);
    $publish->publish($message);
} catch (PublishException $e) {
    // Catch publish errors
} catch (\Exception $e) {
    // Catch all other errors
}
?>
```

## Usage Example - Consume

The consumer will retrieve messages from the queue and pass them to an object/method defined by the user for processing.


```php
<?php
use PhpAmqpLib\Connection\AMQPConnection;
use RabbitQueue\Services\Consume;
use RabbitQueue\Exceptions\ConsumeException;

// A class containing a method that the consumer can send the retrieved message body
try {
    $amqp           = new AMQPConnection('http://myrabbithost', 5672, 'guest', 'guest');
    $processObject  = new ExampleProcessClass();
    $consume        = new Consume($amqp);
    $consume->consume('EXAMPLE_QUEUE', $processObject, 'exampleProcessMethod');
} catch (ConsumeException $e) {
    // Catch publish errors
} catch (\Exception $e) {
    // Catch all other errors
}
?>
```