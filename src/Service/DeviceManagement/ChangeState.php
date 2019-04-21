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

class ChangeState
{
    /**
     * @var \App\Model\Device\Device
     */
    private $deviceModel;

    /**
     * ChangeState constructor.
     * @param \App\Model\Device\Device $device
     */
    public function __construct(\App\Model\Device\Device $device)
    {
        $this->deviceModel = $device;
    }

    /**
     * @param Device $device
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function change(Device $device)
    {
        switch ($device->getDeviceType()){
            case DeviceType::DOOR:{
                switch ($device->getState()){
                    case StateType::DOOR_UNLOCKED:{
                        $device->setState(StateType::DOOR_LOCKED);
                        $this->deviceModel->updateState($device);
                        break;
                    }
                    case StateType::DOOR_LOCKED:{
                        $device->setState(StateType::DOOR_UNLOCKED);
                        $this->deviceModel->updateState($device);
                        break;
                    }
                }
                break;
            }
            case DeviceType::LIGHT:{
                switch ($device->getState()){
                    case StateType::LIGHT_TURNED_ON:{
                        $device->setState(StateType::LIGHT_TURNED_OFF);
                        $this->deviceModel->updateState($device);
                        break;
                    }
                    case StateType::LIGHT_TURNED_OFF:{
                        $device->setState(StateType::LIGHT_TURNED_ON);
                        $this->deviceModel->updateState($device);
                        break;
                    }
                }
                break;
            }
            case DeviceType::BLINDS:{
                switch ($device->getState()){
                    case StateType::BLINDS_ROLLED_UP:{
                        $device->setState(StateType::BLINDS_ROLLED_DOWN);
                        $this->deviceModel->updateState($device);
                        break;
                    }
                    case StateType::BLINDS_ROLLED_DOWN:{
                        $device->setState(StateType::BLINDS_ROLLED_UP);
                        $this->deviceModel->updateState($device);
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
    public function correct(Device $device, string $rotation)
    {
        switch ($device->getDeviceType()){
            case DeviceType::BLINDS:{
                switch ($rotation){
                    case 'DOWN':{
                        $device->setState(StateType::BLINDS_ROLLED_UP);
                        $this->deviceModel->correctState($device);
                        break;
                    }
                    case 'UP':{
                        $device->setState(StateType::BLINDS_ROLLED_DOWN);
                        $this->deviceModel->correctState($device);
                        break;
                    }
                }
                break;
            }
        }
    }
}