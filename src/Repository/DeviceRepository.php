<?php

namespace App\Repository;

use App\Entity\Device;
use App\Model\Device\DeviceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Device::class);
    }

    /**
     * @return Device[]
     */
    public function findAllDoorDevices()
    {
        return $this->findBy(['deviceType' => DeviceType::DOOR, 'status' => 1]);
    }

    /**
     * @param Device $device
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Device $device)
    {
        $this->getEntityManager()->persist($device);
        $this->getEntityManager()->flush();
    }
}
