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
    const LOCKED_DOOR = 1;
    const UNLOCKED_DOOR = 2;
    const TURNED_ON_LIGHT = 3;
    const TURNED_OFF_LIGHT = 4;
}