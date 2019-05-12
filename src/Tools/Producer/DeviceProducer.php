<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 07.05.19
 * Time: 19:32
 */

namespace App\Tools\Producer;


use App\Tools\Producer\Util\ProducerAbstract;
use App\Tools\Producer\Util\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class DeviceProducer extends ProducerAbstract implements ProducerInterface
{
    const NAME = 'device';

    /**
     * DeviceProducer constructor.
     * @throws Util\InvalidConfigurationException
     */
    public function __construct()
    {
        $this->connect()->declareQueue(self::NAME);
    }

    /**
     * @param AMQPMessage $message
     * @param string $routingKey
     * @param array $options
     */
    public function publish(AMQPMessage $message, string $routingKey = '', array $options = []): void
    {
        $this->channel->basic_publish($message, '', !empty($routingKey) ? $routingKey : self::NAME);
    }
}