<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:51
 */

namespace App\Service\DeviceManagement;

interface DeviceChangeStateInterface
{
    public function changeState(): void;
}