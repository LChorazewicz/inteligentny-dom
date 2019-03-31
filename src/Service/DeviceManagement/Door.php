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
    public function changeState(LoggerInterface $logger, int $state, int $pin)
    {
        $outputState = null;
        $command = "cd ../src/Scripts && python door.py " . $pin;
        $logger->info("Change door state in progress", ['state' => $state, 'pin' => $pin]);
        switch ($state){
            case StateType::LOCKED:{
                $command = $command  . " 1";
                $logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
            case StateType::UNLOCKED_DOOR:{
                $command = $command  . " 2";
                $logger->info("run ", ['command' => $command]);
                $outputState = exec($command);
                break;
            }
        }

        $logger->info('response status', ['output' => $outputState]);

        return $outputState;
    }

}