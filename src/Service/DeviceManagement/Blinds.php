<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:52
 */

namespace App\Service\DeviceManagement;


use App\Entity\Device;
use App\Model\Device\DeviceAction;
use App\Model\Device\DeviceDirection;
use App\Model\Device\StateType;
use App\Repository\DeviceRepository;
use App\Tools\Logger;
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
        Logger::getLogger('service/device', Logger::INFO, 'blinds')->info('move-by-step',
            ['current_turn' => $currentTurn, 'turns' => $turns]
        );
        $this->execScript($turns, 256, true);
    }

    /**
     * @param int $turns
     * @param int $engineStepsPerCicle
     * @param bool $updataData
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    private function execScript(int $turns, int $engineStepsPerCicle, bool $updataData)
    {
        $outputState = null;

        $gpioMock = isset($_ENV['GPIO_MOCK']) && filter_var($_ENV['GPIO_MOCK'], FILTER_VALIDATE_BOOLEAN);

        $pins = $this->getPinsForPythonScript($this->device->getPins());
        $previousState = $this->device->getState();
        $command = "cd " . dirname(__DIR__) . "/../Scripts && python motor.py " . $pins . " " . $this->inWhichWay . " " . $engineStepsPerCicle;
        $this->logger->info("Change motor state in progress", ['state' => $previousState, 'pins' => $pins, 'turns' => $turns, 'command' => $command]);
        Logger::getLogger('service/device', Logger::INFO, 'blinds')->info(
            'Change motor state in progress',
            ['state' => $previousState, 'pins' => $pins, 'turns' => $turns, 'command' => $command,
                'GPIO_MOCK' => $gpioMock, 'update_data' => $updataData]
        );

        switch ($previousState){
            case StateType::BLINDS_ROLLED_UP:
            case StateType::BLINDS_ROLLED_DOWN:{
                for($i = 1; $i <= $turns; $i++){
                    $this->updateState();

                    $outputState = null;
                    if($gpioMock){
                        $outputState = 1;
                        Logger::getLogger('service/device', Logger::INFO, 'blinds')->info('output state mock', ['output' => $outputState]);
                    }else{
                        $outputState = exec($command);
                        Logger::getLogger('service/device', Logger::INFO, 'blinds')->info('output state gpio', ['output' => $outputState]);
                    }

                    if($updataData){
                        $nextTurn = $this->addOrMinusAndGetStep($this->inWhichWay);
                        $this->device->setCurrentAction($this->specifyCurrentAction($nextTurn));
                        $this->device->setCurrentTurn($nextTurn);

                        $this->deviceRepository->update($this->device);
                    }
                }
                $this->device->setCurrentAction(DeviceAction::INACTIVE);
                $this->updateState();
                break;
            }
            default:{
                throw new \Exception("Unknown state type " . $previousState);
            }
        }

        $this->logger->info('response status', ['output' => $outputState]);
        Logger::getLogger('service/device', Logger::INFO, 'blinds')->info('response status', ['output' => $outputState]);
        if(($gpioMock && $updataData) || ($updataData && $outputState == 1)){
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

    private function specifyCurrentAction(int $nextTurn)
    {
        $return = 0;

        $turn = $nextTurn;
        $currentTurn = $this->device->getCurrentTurn();

        switch ($this->device->getDeviceDirection()){
            case DeviceDirection::LEFT:{
                if($turn > $currentTurn){
                    $return = DeviceAction::OPENING;
                }elseif($turn <$currentTurn){
                    $return = DeviceAction::CLOSING;
                }
                break;
            }

            case DeviceDirection::RIGHT:{
                if($turn > $currentTurn){
                    $return = DeviceAction::OPENING;
                }elseif($turn <$currentTurn){
                    $return = DeviceAction::CLOSING;
                }
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_LEFT:{
                if($turn > $currentTurn){
                    $return = DeviceAction::OPENING;
                }elseif($turn <$currentTurn){
                    $return = DeviceAction::CLOSING;
                }
                break;
            }

            case DeviceDirection::UPSIDE_DOWN_RIGHT:{
                if($turn > $currentTurn){
                    $return = DeviceAction::OPENING;
                }elseif($turn <$currentTurn){
                    $return = DeviceAction::CLOSING;
                }
                break;
            }
        }


        return $return;
    }
}