<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 13:09
 */

namespace App\Service\DeviceManagement;


use App\Model\Device\StateType;
use Psr\Log\LoggerInterface;

class Door
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Door constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $state
     * @param int $pin
     * @return string|null
     * @throws \Exception
     */
    public function changeState(int $state, int $pin)
    {
        $outputState = null;
        $command = "cd ../src/Scripts && python door.py " . $pin;
        $this->logger->info("Change door state in progress", ['state' => $state, 'pin' => $pin]);
        switch ($state){
            case StateType::DOOR_UNLOCKED:{
                $command = $command  . " 1";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            case StateType::DOOR_LOCKED:{
                $command = $command  . " 2";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            default:
                throw new \Exception("Unknown state type " . $state);
        }

        $this->logger->info('response status', ['output' => $outputState]);

        return $outputState;
    }

}