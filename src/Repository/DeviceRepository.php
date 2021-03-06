<?php

namespace App\Repository;

use App\Entity\Device;
use App\Model\Device\DeviceType;
use App\Model\Device\Dto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Device|null find($id, $lockMode = null, $lockVersion = null)
 * @method Device|null findOneBy(array $criteria, array $orderBy = null)
 * @method Device[]    findAll()
 * @method Device[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceRepository extends ServiceEntityRepository
{
    /**
     * DeviceRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Device::class);
    }

    /**
     * @return Device[]
     */
    public function findAllDevices()
    {
        return $this->findBy(['status' => 1]);
    }

    /**
     * @param Device $device
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Device $device)
    {
        $this->getEntityManager()->persist($device);
        $this->getEntityManager()->flush();
        return $device->getId();
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

    /**
     * @param $deviceId
     * @return Device
     */
    public function findDevice($deviceId)
    {
        return $this->findOneBy(['id' => $deviceId, 'status' => 1]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAll()
    {
        $entities = $this->findAll();
        $em = $this->getEntityManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
        }
        $em->flush();
    }
}
