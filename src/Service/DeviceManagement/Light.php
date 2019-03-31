<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:52
 */

namespace App\Service\DeviceManagement;


use App\Model\Device\StateType;
use Psr\Log\LoggerInterface;

class Light implements DeviceChangeStateInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Light constructor.
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
     */
    public function changeState(int $state, int $pin)
    {
        $outputState = null;
        $command = "cd ../src/Scripts && python door.py " . $pin;
        $this->logger->info("Change light state in progress", ['state' => $state, 'pin' => $pin]);
        switch ($state){
            case StateType::UNLOCKED_DOOR:{
                $command = $command  . " 1";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            case StateType::LOCKED_DOOR:{
                $command = $command  . " 2";
                $this->logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
        }

        $this->logger->info('response status', ['output' => $outputState]);

        return $outputState;
    }
}