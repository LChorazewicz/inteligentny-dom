<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $deviceType;

    /**
     * @ORM\Column(type="string")
     */
    private $pins;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $turns;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $currentTurn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Device
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return Device
     */
    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeviceType(): int
    {
        return $this->deviceType;
    }

    /**
     * @param int $deviceType
     * @return Device
     */
    public function setDeviceType(int $deviceType): self
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * @return string
     */
    public function getPins(): ?string
    {
        return $this->pins;
    }

    /**
     * @param array $pins
     * @return Device
     */
    public function setPins(array $pins): self
    {
        $this->pins = implode(',', $pins);

        return $this;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return Device
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTurns(): ?int
    {
        return $this->turns;
    }

    /**
     * @param int $turns
     * @return Device
     */
    public function setTurns(?int $turns): Device
    {
        $this->turns = $turns;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrentTurn(): ?int
    {
        return $this->currentTurn;
    }

    /**
     * @param int $currentTurn
     * @return Device
     */
    public function setCurrentTurn(?int $currentTurn)
    {
        $this->currentTurn = $currentTurn;
        return $this;
    }


}
