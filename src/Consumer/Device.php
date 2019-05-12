<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 06.05.19
 * Time: 17:19
 */

namespace App\Consumer;


use App\Service\DeviceManagement\ChangeState;
use App\Tools\Logger;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class Device implements ConsumerInterface
{
    /**
     * @var \App\Model\Device\Device
     */
    private $deviceModel;

    /**
     * @var ChangeState
     */
    private $changeState;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ApiController constructor.
     * @param \App\Model\Device\Device $device
     * @param ChangeState $changeState
     * @throws \Exception
     */
    public function __construct(\App\Model\Device\Device $device, ChangeState $changeState)
    {
        $this->deviceModel = $device;
        $this->changeState = $changeState;
        $this->logger = Logger::getLogger('consumer/device', Logger::INFO, 'consumer');
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $body = (array)json_decode($msg->getBody());

        try{
            $deviceId = $body['device_id'];
            $step = $body['step'];
            $device = $this->deviceModel->getDevice($deviceId);
            if($device !== null){
                $this->changeState->moveByStep($device, $step);
            }
        }catch (\Throwable $throwable){
            $this->logger->error($throwable->getMessage());
        }
        return true;
    }
}