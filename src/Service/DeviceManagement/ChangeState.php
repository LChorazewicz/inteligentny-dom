<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 20:47
 */

namespace App\Service\DeviceManagement;


use App\Entity\Device;
use App\Model\Device\DeviceType;
use App\Model\Device\StateType;
use App\Repository\DeviceRepository;
use Psr\Log\LoggerInterface;

class ChangeState
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * ChangeState constructor.
     * @param LoggerInterface $logger
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(LoggerInterface $logger, DeviceRepository $deviceRepository)
    {
        $this->logger = $logger;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param Device $device
     * @throws \Exception
     */
    public function change(Device $device)
    {
        $result = null;
        switch ($device->getDeviceType()) {
            case DeviceType::DOOR:{
                switch ($device->getState()) {
                    case StateType::DOOR_UNLOCKED:
                    case StateType::DOOR_LOCKED:{
                        (new Door($device, $this->logger, $this->deviceRepository))->changeState();
                        break;
                    }
                }
                break;
            }
            case DeviceType::LIGHT:{
                switch ($device->getState()) {
                    case StateType::LIGHT_TURNED_ON:
                    case StateType::LIGHT_TURNED_OFF:{
                        (new Light($device, $this->logger, $this->deviceRepository))->changeState();
                        break;
                    }
                }
                break;
            }
            case DeviceType::BLINDS:{
                switch ($device->getState()){
                    case StateType::BLINDS_ROLLED_DOWN:
                    case StateType::BLINDS_ROLLED_UP: {
                        (new Blinds($device, $this->logger, $this->deviceRepository))->changeState();
                        break;
                    }
                }
                break;
            }
        }
    }

    /**
     * @param Device $device
     * @param int $percent
     * @throws \Exception
     */
    public function moveByStep(Device $device, int $percent)
    {
        $result = null;
        switch ($device->getDeviceType()){
            case DeviceType::BLINDS:{
                switch ($device->getState()){
                    case StateType::BLINDS_ROLLED_DOWN:
                    case StateType::BLINDS_ROLLED_UP: {
                        (new Blinds($device, $this->logger, $this->deviceRepository))->moveByStep($percent);
                        break;
                    }
                }
                break;
            }
        }
    }

    /**
     * @param Device $device
     * @param string $rotation
     * @throws \Exception
     */
    public function correctState(Device $device, string $rotation)
    {
        switch ($device->getDeviceType()){
            case DeviceType::BLINDS:{
                switch ($rotation){
                    case 'DOWN':
                    case 'UP': {
                        $state = $rotation == 'DOWN' ? StateType::BLINDS_ROLLED_UP : StateType::BLINDS_ROLLED_DOWN;
                        $device->setState($state);
                        (new Blinds($device, $this->logger, $this->deviceRepository))->correctState();
                        break;
                    }
                }
                break;
            }
        }
    }
}