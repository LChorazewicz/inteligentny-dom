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
        $this->execScript(1, 16, $this->inWhichWay($this->device->getDeviceDirection()), false);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeState(): void
    {
        $turns = $this->device->getTurns();
        if($this->device->getState() === StateType::BLINDS_ROLLED_UP){
            $turns = 0;
        }
        $this->execScript($this->setTurns($turns), 256, $this->inWhichWay, true);
    }

    /**
     * @param int $step
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function moveByStep(int $step)
    {
        $this->execScript($this->setTurns($step), 256, $this->inWhichWay, true);
    }

    /**
     * @param int $turns
     * @param int $engineStepsPerCicle
     * @param int $whichWay
     * @param bool $updataData
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function execScript(int $turns, int $engineStepsPerCicle, int $whichWay, bool $updataData)
    {
        $outputState = null;
        $pins = $this->getPinsForPythonScript($this->device->getPins());
        $previousState = $this->device->getState();
        $command = "cd " . dirname(__DIR__) . "/../Scripts && python motor.py " . $pins . " " . $whichWay . " " . $engineStepsPerCicle;
        $this->logger->info("Change motor state in progress", ['state' => $previousState, 'pins' => $pins, 'turns' => $turns, 'command' => $command]);

        switch ($previousState){
            case StateType::BLINDS_ROLLED_UP:
            case StateType::BLINDS_ROLLED_DOWN:{
                for($i = 1; $i <= $turns; $i++){
                    $outputState = !GPIO_MOCK ? exec($command) : 1;

                    if($updataData){
                        $this->device->setCurrentTurn($this->addOrMinusAndGetStep($whichWay));
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

    private function setTurns(int $turn): int
    {
        $return = 0;
        $currentTurn = $this->device->getCurrentTurn();
        if($turn > $currentTurn){
            $return = $turn - $currentTurn;
            $this->inWhichWay = self::ENGINE_TURN_UP;
        }elseif($turn < $this->device->getCurrentTurn()){
            $return = $currentTurn - $turn;
            $this->inWhichWay = self::ENGINE_TURN_DOWN;
        }else{
            $this->inWhichWay = self::ENGINE_TURN_DOWN;
        }

        return $return;
    }

    private function addOrMinusAndGetStep(int $whichWay): int
    {
        $result = 0;
        switch ($whichWay){
            case self::ENGINE_TURN_UP:{
                $result = $this->device->getCurrentTurn() + 1;
                break;
            }
            case self::ENGINE_TURN_DOWN:{
                $result = $this->device->getCurrentTurn() - 1;
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