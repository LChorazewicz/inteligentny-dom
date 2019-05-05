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

class Blinds extends DeviceAbstract implements CorrectMotorInterface
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
     * @var int
     */
    private $inWhichWay;

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
    public function correctState(): void
    {
        $currentTurn = !is_null($this->device->getCurrentTurn()) ? $this->device->getCurrentTurn() : 0;
        $copyOfCurrentTurn = $currentTurn;
        if($this->device->getState() === StateType::BLINDS_ROLLED_UP){
            $turn = $copyOfCurrentTurn += 1;
        }elseif ($this->device->getState() === StateType::BLINDS_ROLLED_DOWN){
            $turn = $copyOfCurrentTurn -= 1;
        }

        $turns = $this->setTurnsForDeviceWithSpecificDirection($turn, $currentTurn, $this->device->getDeviceDirection());

        $this->execScript($turns, 16, false);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeState(): void
    {
        $deviceTurns = $this->device->getTurns();
        if($this->device->getState() === StateType::BLINDS_ROLLED_UP){
            $deviceTurns = 0;
        }

        $currentTurn = !is_null($this->device->getCurrentTurn()) ? $this->device->getCurrentTurn() : 0;
        $turns = $this->setTurnsForDeviceWithSpecificDirection($deviceTurns, $currentTurn, $this->device->getDeviceDirection());

        $this->execScript($turns, 256, true);
    }

    /**
     * @param int $step
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function moveByStep(int $step)
    {
        $currentTurn = !is_null($this->device->getCurrentTurn()) ? $this->device->getCurrentTurn() : 0;
        $turns = $this->setTurnsForDeviceWithSpecificDirection($step, $currentTurn, $this->device->getDeviceDirection());
        $this->execScript($turns, 256, true);
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
        $command = "cd " . dirname(__DIR__) . "/../Scripts && python motor.py " . $pins . " " . $this->inWhichWay . " " . $engineStepsPerCicle;
        $this->logger->info("Change motor state in progress", ['state' => $previousState, 'pins' => $pins, 'turns' => $turns, 'command' => $command]);

        switch ($previousState){
            case StateType::BLINDS_ROLLED_UP:
            case StateType::BLINDS_ROLLED_DOWN:{
                for($i = 1; $i <= $turns; $i++){
                    $outputState = !GPIO_MOCK ? exec($command) : 1;

                    if($updataData){
                        $this->device->setCurrentTurn($this->addOrMinusAndGetStep($this->inWhichWay));
                    }
                }
                $this->updateState();
                break;
            }
            default:{
                throw new \Exception("Unknown state type " . $previousState);
            }
        }

        $this->logger->info('response status', ['output' => $outputState]);

        if((GPIO_MOCK && $updataData) || ($updataData && $outputState == 1)){
            $this->deviceRepository->update($this->device);
        }
    }

    private function setTurnsForDeviceWithSpecificDirection(int $turn, int $currentTurn, int $deviceDirection): int
    {
        $return = 0;
        $this->inWhichWay = self::ENGINE_TURN_DOWN;

        if($turn > $currentTurn){
            $return = $turn - $currentTurn;
        }elseif($turn <$currentTurn){
            $return = $currentTurn - $turn;
        }

        switch ($deviceDirection){
            case DeviceDirection::LEFT:{
                if($turn > $currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_UP;
                }elseif($turn <$currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_DOWN;
                }
                break;
            }

            case DeviceDirection::RIGHT:{
                if($turn > $currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_DOWN;
                }elseif($turn <$currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_UP;
                }
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_LEFT:{
                if($turn > $currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_UP;
                }elseif($turn <$currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_DOWN;
                }
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_RIGHT:{
                if($turn > $currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_UP;
                }elseif($turn <$currentTurn){
                    $this->inWhichWay = self::ENGINE_TURN_DOWN;
                }
                break;
            }
        }


        return $return;
    }

    private function addOrMinusAndGetStep(int $whichWay): int
    {
        $result = 0;
        switch ($this->device->getDeviceDirection()){
            case DeviceDirection::LEFT:{
                if($whichWay === self::ENGINE_TURN_UP){
                    $result = $this->device->getCurrentTurn() + 1;
                }elseif($whichWay === self::ENGINE_TURN_DOWN){
                    $result = $this->device->getCurrentTurn() - 1;
                }
                break;
            }
            case DeviceDirection::RIGHT:{
                if($whichWay === self::ENGINE_TURN_UP){
                    $result = $this->device->getCurrentTurn() - 1;
                }elseif($whichWay === self::ENGINE_TURN_DOWN){
                    $result = $this->device->getCurrentTurn() + 1;
                }
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_LEFT:{
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_RIGHT:{
                break;
            }
        }

        return $result;
    }

    private function updateState()
    {
        if($this->device->getCurrentTurn() > 0 && $this->device->getCurrentTurn() <= $this->device->getTurns()){
            $this->device->setState(StateType::BLINDS_ROLLED_UP);
        }

        if($this->device->getCurrentTurn() === 0){
            $this->device->setState(StateType::BLINDS_ROLLED_DOWN);
        }
    }
}