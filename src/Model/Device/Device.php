<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 12:42
 */

namespace App\Model\Device;


use App\Repository\DeviceRepository;
use App\Service\DeviceManagement\Blinds;
use App\Service\DeviceManagement\Door;
use App\Service\DeviceManagement\Light;
use Psr\Log\LoggerInterface;

class Device
{
    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(DeviceRepository $deviceRepository, LoggerInterface $logger)
    {
        $this->deviceRepository = $deviceRepository;
        $this->logger = $logger;
    }

    /**
     * @return Dto[]
     */
    public function findAllDevicesDto()
    {
        $result = $this->deviceRepository->findAllDevices();
        $dtos = [];
        foreach ($result as $device){
            $dtos[$device->getId()] = (array)$this->mapDeviceToDto($device);
        }
        return $dtos;
    }

    public function mapDeviceToDto(\App\Entity\Device $device)
    {
        $deviceDto = new Dto();
        $deviceDto->deviceId = $device->getId();
        $deviceDto->deviceName = $device->getName();
        $deviceDto->state = $device->getState();
        $deviceDto->stateName = $this->mapStateName($device->getDeviceType(), $device->getState());
        $deviceDto->deviceType = $device->getDeviceType();
        $deviceDto->deviceTypeName = $this->mapDeviceTypeName($device->getDeviceType());
        $deviceDto->pins = empty($device->getPins()) ? null : $device->getPins();
        $deviceDto->turns = empty($device->getTurns()) ? null : $device->getTurns();
        $deviceDto->currentTurn = empty($device->getTurns()) ? null : $device->getCurrentTurn();
        $deviceDto->deviceDirection = $device->getDeviceDirection();
        $deviceDto->deviceDirectionName = $this->mapDeviceDirection($device->getDeviceDirection());
        $deviceDto->status = $device->getStatus();
        return $deviceDto;
    }

    /**
     * @param int $deviceId
     * @return \App\Entity\Device
     */
    public function getDevice(int $deviceId)
    {
        return $this->deviceRepository->findOneBy(['id' => $deviceId, 'status' => 1]);
    }

    /**
     * @param \App\Entity\Device $device
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addDevice(\App\Entity\Device $device)
    {
        $knownState = false;
        switch ($device->getDeviceType()){
            case DeviceType::DOOR:{
                if(in_array($device->getState(), StateType::getDoorStates())){
                    $knownState = true;
                }
                break;
            }
            case DeviceType::LIGHT:{
                if(in_array($device->getState(), StateType::getLightStates())){
                    $knownState = true;
                }
                break;
            }
            case DeviceType::BLINDS:{
                if(in_array($device->getState(), StateType::getBlindsStates())){
                    $knownState = true;
                }
                break;
            }
        }

        if(!$knownState){
            throw new \Exception("Unknown device");
        }

        return $this->deviceRepository->add($device);
    }

    public function getDeviceDto($deviceId)
    {
        $device = $this->deviceRepository->findDevice($deviceId);
        $response = [];
        if(!empty($device)){
            $response = $this->mapDeviceToDto($device);
        }
        return $response;
    }

    private function mapStateName(int $deviceType, int $deviceState)
    {
        $result = "";
        switch ($deviceType){
            case DeviceType::DOOR:{
                switch ($deviceState){
                    case StateType::DOOR_LOCKED:{
                        $result = "Locked";
                        break;
                    }
                    case StateType::DOOR_UNLOCKED:{
                        $result = "Unlocked";
                        break;
                    }
                }
                break;
            }
            case DeviceType::LIGHT:{
                switch ($deviceState){
                    case StateType::LIGHT_TURNED_ON:{
                        $result = "Turned on";
                        break;
                    }
                    case StateType::LIGHT_TURNED_OFF:{
                        $result = "Turned off";
                        break;
                    }
                }
                break;
            }
            case DeviceType::BLINDS:{
                switch ($deviceState){
                    case StateType::BLINDS_ROLLED_UP:{
                        $result = "Rolled up";
                        break;
                    }
                    case StateType::BLINDS_ROLLED_DOWN:{
                        $result = "Rolled down";
                        break;
                    }
                }
                break;
            }
        }
        return $result;
    }

    private function mapDeviceTypeName(int $deviceType)
    {
        $result = "";
        switch ($deviceType){
            case DeviceType::DOOR:{
                $result = "Door";
                break;
            }
            case DeviceType::LIGHT:{
                $result = "Light";
                break;
            }
            case DeviceType::BLINDS:{
                $result = "Blinds";
                break;
            }
        }
        return $result;
    }
    /**
     * @param int|null $getDeviceDirection
     * @return string|null
     */
    public function mapDeviceDirection(?int $getDeviceDirection): ?string
    {
        $result = "";
        switch ($getDeviceDirection){
            case DeviceDirection::LEFT: {
                $result = "Left";
                break;
            }
            case DeviceDirection::RIGHT: {
                $result = "Right";
                break;
            }
            case DeviceDirection::UPSIDE_DOWN_LEFT: {
                $result = "Upside down - left";
                break;
            }
            case DeviceDirection::UPSIDE_DOWN_RIGHT: {
                $result = "Upside down - right";
                break;
            }
        }
        return $result;
    }
}