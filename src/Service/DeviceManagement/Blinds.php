<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:52
 */

namespace App\Service\DeviceManagement;


use App\Entity\Device;
use App\Model\Device\DeviceDirection;
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
        $this->execScript($this->device->getTurns(), 256, true);
    }

    /**
     * @throws \Exception
     */
    public function correctState(): void
    {
        $this->execScript(1, 16, false);
    }

    /**
     * @param int $turns
     * @param int $engineStepsPerCicle
     * @param bool $updataData
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function execScript(int $turns, int $engineStepsPerCicle, bool $updataData)
    {
        $outputState = null;
        $pins = $this->getPinsForPythonScript($this->device->getPins());
        $previousState = $this->device->getState();
        $command = "cd ../src/Scripts && python motor.py " . $pins;
        $this->logger->info("Change motor state in progress", ['state' => $previousState, 'pins' => $pins, 'turns' => $turns]);
        $whichWay = $this->inWhichWay($this->device->getDeviceDirection());

        switch ($previousState){
            case StateType::BLINDS_ROLLED_UP:{
                $command = $command  . " " . $whichWay . " " . $engineStepsPerCicle;
                $this->logger->info("run ", ['command' => $command]);
                for($i = 1; $i <= $turns; $i++){
                    $outputState = exec($command);
                    if($updataData){
                        $this->device->setCurrentTurn($this->device->getCurrentTurn() - 1);
                    }
                }
                $this->device->setState(StateType::BLINDS_ROLLED_DOWN);
                break;
            }
            case StateType::BLINDS_ROLLED_DOWN:{
                $command = $command  . " " . $whichWay . " " . $engineStepsPerCicle;
                $this->logger->info("run ", ['command' => $command]);
                for($i = 1; $i <= $turns; $i++){
                    $outputState = exec($command);
                    if($updataData){
                        $this->device->setCurrentTurn($this->device->getCurrentTurn() + 1);
                    }
                }
                $this->device->setState(StateType::BLINDS_ROLLED_UP);
                break;
            }
            default:
                throw new \Exception("Unknown state type " . $previousState);
        }

        $this->logger->info('response status', ['output' => $outputState]);

        if((GPIO_MOCK && $updataData) || ($updataData && $outputState == 1)){
            $this->deviceRepository->update($this->device);
        }
    }

    private function inWhichWay(?int $getDeviceDirection)
    {
        $way = self::ENGINE_TURN_UP;
        switch ($getDeviceDirection){
            case DeviceDirection::LEFT:{
                $way = self::ENGINE_TURN_UP;
                break;
            }
            case DeviceDirection::RIGHT:{
                $way = self::ENGINE_TURN_DOWN;
                break;
            }
            case DeviceDirection::UPSIDE_DOWN_LEFT:
            case DeviceDirection::UPSIDE_DOWN_RIGHT:{
                //todo
                break;
            }
        }
        return $way;
    }
}