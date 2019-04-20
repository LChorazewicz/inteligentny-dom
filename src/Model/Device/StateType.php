<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 16.03.19
 * Time: 12:36
 */

namespace App\Model\Device;


class StateType
{
    const DOOR_LOCKED = 1;
    const DOOR_UNLOCKED = 2;
    const LIGHT_TURNED_ON = 3;
    const LIGHT_TURNED_OFF = 4;
    const BLINDS_ROLLED_UP = 5;
    const BLINDS_ROLLED_DOWN = 6;

    /**
     * @return array
     */
    public static function getDoorStates(): array
    {
        return [
            self::DOOR_LOCKED, self::DOOR_UNLOCKED
        ];
    }

    /**
     * @return array
     */
    public static function getLightStates(): array
    {
        return [
            self::LIGHT_TURNED_ON, self::LIGHT_TURNED_OFF
        ];
    }

    /**
     * @return array
     */
    public static function getBlindsStates(): array
    {
        return [
            self::BLINDS_ROLLED_UP, self::BLINDS_ROLLED_DOWN
        ];
    }
}