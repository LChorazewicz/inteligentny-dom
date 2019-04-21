<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 14.04.19
 * Time: 12:51
 */

namespace App\Service\RaspberryService;


use App\Repository\PinRepository;

/**
 * Class RaspberryService
 * @package App\Service\RaspberryService
 */
class RaspberryService implements RaspberryServiceInterface
{
    /**
     * @var PinRepository
     */
    private $pinReponsitory;

    /**
     * RaspberryService constructor.
     * @param PinRepository $pinRepository
     */
    public function __construct(PinRepository $pinRepository)
    {
        $this->pinReponsitory = $pinRepository;
    }

    /**
     * @return array
     */
    public function getAvailablePins(): array
    {
        return $this->pinReponsitory->getAllAvailablePins();
    }

    /**
     * @param int $pin
     * @return int
     */
    public function getPinMode(int $pin): int
    {
        return $this->pinReponsitory->getPinMode($pin);
    }

    /**
     * @param int $pin
     * @return string
     */
    public function getPinName(int $pin): string
    {
        return $this->pinReponsitory->getPinName($pin);
    }

    /**
     * @param int $pin
     * @return int
     */
    public function getPhysicalPinId(int $pin): int
    {
        return $this->pinReponsitory->getPinPhysicalId($pin);
    }

    /**
     * @param int $pin
     * @param int $mode
     * @return RaspberryService
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPinMode(int $pin, int $mode): RaspberryService
    {
        $entity = $this->pinReponsitory->getPin($pin);
        if(!empty($entity)){
            $command = "cd ../../src/Scripts && python setpinmode.py " . $entity->getBcmId() . " $mode";
            $outputState = exec($command);

            if($outputState == "success"){
                $this->pinReponsitory->setPinMode($pin, $mode);
            }
        }
        return $this;
    }

    /**
     * @param int $pin
     * @param int $state
     * @return RaspberryService
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPinState(int $pin, int $state): RaspberryService
    {
        $entity = $this->pinReponsitory->getPin($pin);
        if(!empty($entity)){
            $command = "cd ../../src/Scripts && python setpinstate.py " . $entity->getBcmId() . " $state";
            $outputState = exec($command);

            if($outputState == "success"){
                $this->pinReponsitory->setPinState($pin, $state);

            }
        }
        return $this;
    }

    /**
     * @param int $pin
     * @return int
     */
    public function getPinState(int $pin): int
    {
        return $this->pinReponsitory->getPinState($pin);
    }

    /**
     * @return RaspberryService
     */
    public function clearPins(): RaspberryService
    {
        $command = "cd ../../src/Scripts && python resetpins.py ";
        exec($command);
        return $this;
    }

    /**
     * @return RaspberryService
     */
    public function reload(): RaspberryService
    {
        //todo
        return $this;
    }
}