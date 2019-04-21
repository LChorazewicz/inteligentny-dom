<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 14.04.19
 * Time: 12:52
 */

namespace App\Service\RaspberryService;


interface RaspberryServiceInterface
{
    public function getAvailablePins(): array;

    public function getPinMode(int $pin): int;

    public function getPinName(int $pin): string;

    public function getPhysicalPinId(int $pin): int;

    public function setPinMode(int $pin, int $mode): RaspberryService;

    public function setPinState(int $pin, int $state): RaspberryService;

    public function getPinState(int $pin): int;

    public function clearPins(): RaspberryService;
}