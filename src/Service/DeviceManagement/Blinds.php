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

class Blinds extends DeviceAbstract implements DeviceChangeStateInterface, CorrectMotorInterface
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
        $this->execScript($this->device->getTurns(), 256);
    }

    /**
     * @throws \Exception
     */
    public function correctState(): void
    {
        $this->execScript(1, 64);
    }

    /**
     * @param int $turns
     * @param int $engineStepsPerCicle
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function execScript(int $turns, int $engineStepsPerCicle)
    {
        $outputState = null;
        $pins = $this->getPinsForPythonScript($this->device->getPins());
        $state = $this->device->getState();
        $command = "cd ../src/Scripts && python motor.py " . $pins;
        $this->logger->info("Change motor state in progress", ['state' => $state, 'pins' => $pins, 'turns' => $turns]);
        $deviceTurns = ($this->device->getTurns() != null) ? $this->device->getTurns() : 0;

        switch ($state){
            case StateType::BLINDS_ROLLED_UP:{
                $command = $command  . " 1 " . $engineStepsPerCicle;
                $this->logger->info("run ", ['command' => $command]);
                for($i = 0; $i <= $turns - 1; $i++){
                    $outputState = exec($command);
                    $this->device->setCurrentTurn($deviceTurns - 1);
                }
                $this->device->setState(StateType::BLINDS_ROLLED_DOWN);
                break;
            }
            case StateType::BLINDS_ROLLED_DOWN:{
                $command = $command  . " 2" . $engineStepsPerCicle;
                $this->logger->info("run ", ['command' => $command]);
                for($i = 0; $i <= $turns - 1; $i++){
                    $outputState = exec($command);
                    $this->device->setCurrentTurn($deviceTurns + 1);
                }
                $this->device->setState(StateType::BLINDS_ROLLED_UP);
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