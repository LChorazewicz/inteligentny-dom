<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 21.04.19
 * Time: 14:55
 */

namespace App\Service\DeviceManagement;


abstract class DeviceAbstract
{
    const ENGINE_TURN_UP = 1;

    const ENGINE_TURN_DOWN = 2;

    /**
     * @param string $pins
     * @return string
     */
    public function getPinsForPythonScript(string $pins): string
    {
        return implode(' ', explode(',', $pins));
    }
}