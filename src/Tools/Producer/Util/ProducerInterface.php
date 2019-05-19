<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 07.05.19
 * Time: 19:58
 */

namespace App\Tools\Producer\Util;


use PhpAmqpLib\Message\AMQPMessage;

interface ProducerInterface
{
    public function publish(AMQPMessage $message, string $routingKey = '', array $options = []): void;
}