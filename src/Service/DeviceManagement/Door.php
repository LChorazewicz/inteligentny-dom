<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 13:09
 */

namespace App\Service\DeviceManagement;


use App\Model\Device\StateType;

class Door
{
    public function changeState(int $state, int $pin)
    {
        $outputState = null;
        $command = "cd ../src/Scripts && python door.py " . $pin;
        switch ($state){
            case StateType::LOCKED:{
                $outputState = exec($command  . " 1");
                break;
            }
            case StateType::UNLOCKED:{
                $outputState = exec($command . " 2");
                break;
            }
        }
        return $outputState;
    }

}