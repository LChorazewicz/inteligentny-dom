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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class Device extends ConsumerAbstract implements ConsumerInterface
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ApiController constructor.
     * @param \App\Model\Device\Device $device
     * @param ChangeState $changeState
     * @param EntityManagerInterface $manager
     * @throws \Exception
     */
    public function __construct(\App\Model\Device\Device $device, ChangeState $changeState, EntityManagerInterface $manager)
    {
        $this->deviceModel = $device;
        $this->changeState = $changeState;
        $this->logger = Logger::getLogger('consumer/device', Logger::INFO, 'consumer');
        $this->entityManager = $manager;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        parent::check($this->entityManager);

        $body = (array)json_decode($msg->getBody());

        try{
            $deviceId = $body['device_id'];
            $step = $body['step'];
            $device = $this->deviceModel->getDevice($deviceId);
            Logger::getLogger('consumer/device', Logger::INFO, 'api')->info(
                'consumer', ['device_id' => $deviceId, 'step' => $step]
            );

            if($device !== null){
                $this->changeState->moveByStep($device, $step);
            }
        }catch (\Throwable $throwable){
            $this->logger->error($throwable->getMessage());
        }
        return true;
    }
}