<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 12:42
 */

namespace App\Model\Device;


use App\Repository\DeviceRepository;
use App\Service\DeviceManagement\Door;
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
            $deviceDto = new Dto();
            $deviceDto->deviceId = $device->getId();
            $deviceDto->deviceName = $device->getName();
            $deviceDto->state = $device->getState();
            $deviceDto->stateName = $this->mapStateName($device->getDeviceType(), $device->getState());
            $deviceDto->stateValue = $device->getStateValue();
            $deviceDto->deviceType = $device->getDeviceType();
            $deviceDto->deviceTypeName = $this->mapDeviceTypeName($device->getDeviceType());
            $deviceDto->pin = $device->getPin();
            $deviceDto->status = $device->getStatus();
            $devicesDto[] = $deviceDto;
            $dtos[$device->getId()] = (array)$deviceDto;
        }
        return $dtos;
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
     */
    public function updateState(\App\Entity\Device $device)
    {
        $result = null;
        switch ($device->getDeviceType()){
            case DeviceType::DOOR: {
                $deviceService = new Door();
                $result = $deviceService->changeState($this->logger, $device->getState(), $device->getPin());
                if($result == 1){
                    $this->deviceRepository->update($device);
                }
                break;
            }
            default:{
                throw new \Exception("unknown device type");
            }
        }
    }

    public function getDeviceDto($deviceId)
    {
        $device = $this->deviceRepository->findDevice($deviceId);
        $response = [];
        if(!empty($device)){
            $deviceDto = new Dto();
            $deviceDto->deviceId = $device->getId();
            $deviceDto->deviceName = $device->getName();
            $deviceDto->state = $device->getState();
            $deviceDto->stateName = $this->mapStateName($device->getDeviceType(), $device->getState());
            $deviceDto->stateValue = $device->getStateValue();
            $deviceDto->deviceType = $device->getDeviceType();
            $deviceDto->deviceTypeName = $this->mapDeviceTypeName($device->getDeviceType());
            $deviceDto->pin = $device->getPin();
            $deviceDto->status = $device->getStatus();
            $response = $deviceDto;
        }
        return $response;
    }

    private function mapStateName(int $deviceType, int $deviceState)
    {
        $result = "";
        switch ($deviceType){
            case DeviceType::DOOR:{
                switch ($deviceState){
                    case StateType::LOCKED_DOOR:{
                        $result = "Locked";
                        break;
                    }
                    case StateType::UNLOCKED_DOOR:{
                        $result = "Unlocked";
                        break;
                    }
                }
                break;
            }
            case DeviceType::LIGHT:{
                switch ($deviceState){
                    case StateType::TURNED_ON_LIGHT:{
                        $result = "Turned on";
                        break;
                    }
                    case StateType::TURNED_OFF_LIGHT:{
                        $result = "Turned off";
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
                $result = "Door";
                break;
            }
        }
        return $result;
    }

}