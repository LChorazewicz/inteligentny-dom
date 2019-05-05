<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 21.04.19
 * Time: 15:23
 */

namespace App\Service\DeviceManagement;


interface CorrectMotorInterface
{
    public function correctState(): void;
}