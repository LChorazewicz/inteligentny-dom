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
                    case StateType::UNLOCKED_DOOR:{
                        if($device->getState() == StateType::UNLOCKED_DOOR){
                            $device->setState(StateType::LOCKED_DOOR);
                            $this->deviceModel->updateState($device);
                        }
                        break;
                    }
                    case StateType::LOCKED_DOOR:{
                        if($device->getState() == StateType::LOCKED_DOOR){
                            $device->setState(StateType::UNLOCKED_DOOR);
                            $this->deviceModel->updateState($device);
                        }
                        break;
                    }
                }
                break;
            }
            case DeviceType::LIGHT:{
                switch ($device->getState()){
                    case StateType::TURNED_ON_LIGHT:{
                        if($device->getState() == StateType::TURNED_ON_LIGHT){
                            $device->setState(StateType::TURNED_OFF_LIGHT);
                            $this->deviceModel->updateState($device);
                        }
                        break;
                    }
                    case StateType::TURNED_OFF_LIGHT:{

                        if($device->getState() == StateType::TURNED_OFF_LIGHT){
                            $device->setState(StateType::TURNED_ON_LIGHT);
                            $this->deviceModel->updateState($device);
                        }
                        break;
                    }
                }
                break;
            }
        }
    }
}