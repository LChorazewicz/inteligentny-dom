<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 13:09
 */

namespace App\Service\DeviceManagement;


use App\Entity\Device;
use App\Model\Device\StateType;
use App\Repository\DeviceRepository;
use Psr\Log\LoggerInterface;

class Door extends DeviceAbstract implements DeviceChangeStateInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Device
     */
    private $device;

    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * Door constructor.
     * @param Device $device
     * @param LoggerInterface $logger
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(Device $device, LoggerInterface $logger, DeviceRepository $deviceRepository)
    {
        $this->logger = $logger;
        $this->device = $device;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @throws \Exception
     */
    public function changeState(): void
    {
        $outputState = null;
        $pins = $this->getPinsForPythonScript($this->device->getPins());
        $state = $this->device->getState();
        $command = "cd ../src/Scripts && python door.py " . $pins;
        $this->logger->info("Change door state in progress", ['state' => $state, 'pin' => $pins]);
        switch ($state){
            case StateType::DOOR_UNLOCKED:{
                $command = $command  . " 2";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                $this->device->setState(StateType::DOOR_LOCKED);

                break;
            }
            case StateType::DOOR_LOCKED:{
                $command = $command  . " 1";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                $this->device->setState(StateType::DOOR_UNLOCKED);

                break;
            }
            default:
                throw new \Exception("Unknown state type " . $state);
        }

        $this->logger->info('response status', ['output' => $outputState]);

        if($outputState == 1){
            $this->deviceRepository->update($this->device);
        }
    }

}