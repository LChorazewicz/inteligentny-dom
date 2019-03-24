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
    public function getAllDoorDevicesDto()
    {
        $result = $this->deviceRepository->findAllDoorDevices();
        $dtos = [];
        foreach ($result as $device){
            $dto = new Dto();
            $dto->deviceId = $device->getId();
            $dto->deviceName = $device->getName();
            $dto->state = $device->getState();
            $dtos[$device->getId()] = (array)$dto;
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
}