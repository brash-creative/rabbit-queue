<?php

namespace RabbitQueue\Interfaces;


interface MessageInterface {
    public function setQueue($queue);
    public function getQueue();
    public function setPayload($payload);
    public function getPayload();
    public function __toString();
}