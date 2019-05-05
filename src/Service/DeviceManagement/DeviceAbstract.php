<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 21.04.19
 * Time: 14:55
 */

namespace App\Service\DeviceManagement;


use App\Model\Device\DeviceDirection;

abstract class DeviceAbstract
{
    const ENGINE_TURN_UP = 1;

    const ENGINE_TURN_DOWN = 2;

    /**
     * @param string $pins
     * @return string
     */
    protected function getPinsForPythonScript(string $pins): string
    {
        return implode(' ', explode(',', $pins));
    }

    protected function inWhichWay(?int $getDeviceDirection)
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