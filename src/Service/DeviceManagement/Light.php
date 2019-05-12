<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:52
 */

namespace App\Service\DeviceManagement;


use App\Entity\Device;
use App\Model\Device\StateType;
use App\Repository\DeviceRepository;
use Psr\Log\LoggerInterface;

class Light extends DeviceAbstract implements DeviceChangeStateInterface
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
        $command = "cd ../src/Scripts && python light.py " . $pins;
        $this->logger->info("Change light state in progress", ['state' => $state, 'pin' => $pins]);
        switch ($state){
            case StateType::LIGHT_TURNED_ON:{
                $command = $command  . " 2";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                $this->device->setState(StateType::LIGHT_TURNED_OFF);
                break;
            }
            case StateType::LIGHT_TURNED_OFF:{
                $command = $command  . " 1";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                $this->device->setState(StateType::LIGHT_TURNED_ON);
                break;
            }
            default:
                throw new \Exception("Unknown state type " . $state);
        }

        $this->logger->info('response status', ['output' => $outputState]);

        if(($_ENV['GPIO_MOCK'] && $outputState == 0) || $outputState == 1){
            $this->deviceRepository->update($this->device);
        }
    }
}