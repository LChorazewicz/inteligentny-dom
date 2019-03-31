<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 31.03.19
 * Time: 22:51
 */

namespace App\Service\DeviceManagement;


use Psr\Log\LoggerInterface;

interface DeviceChangeStateInterface
{
    public function __construct(LoggerInterface $logger);

    public function changeState(int $state, int $pin);
}