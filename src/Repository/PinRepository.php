<?php

namespace App\Repository;

use App\Entity\Pin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pin[]    findAll()
 * @method Pin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PinRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pin::class);
    }

    /**
     * @return Pin[]
     */
    public function getAllAvailablePins()
    {
        return $this->findBy(['status' => 1]);
    }

    /**
     * @return Pin[]
     */
    public function getAllFreePins()
    {
        return $this->findBy(['status' => 1, 'free' => true]);
    }

    /**
     * @param int $pin
     * @return string|null
     */
    public function getPinMode(int $pin)
    {
        $result = null;
        $entity = $this->findOneBy(['id' => $pin, 'status' => 1]);
        if(!empty($entity)){
            $result = $entity->getMode();
        }
        return $result;
    }

    /**
     * @param int $pin
     * @return string|null
     */
    public function getPinName(int $pin)
    {
        $result = null;
        $entity = $this->findOneBy(['id' => $pin, 'status' => 1]);
        if(!empty($entity)){
            $result = $entity->getName();
        }
        return $result;
    }

    /**
     * @param int $pin
     * @return int|null
     */
    public function getPinPhysicalId(int $pin)
    {
        $result = null;
        $entity = $this->findOneBy(['id' => $pin, 'status' => 1]);
        if(!empty($entity)){
            $result = $entity->getPhysicalId();
        }
        return $result;
    }

    /**
     * @param int $pin
     * @param int $mode
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPinMode(int $pin, int $mode)
    {
        $pin = $this->findOneBy(['id' => $pin, 'status' => 1]);
        $pin->setMode($mode);
        $this->getEntityManager()->persist($pin);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $pin
     * @return int|null
     */
    public function getPinState(int $pin)
    {
        $result = null;
        $entity = $this->findOneBy(['id' => $pin, 'status' => 1]);
        if(!empty($entity)){
            $result = $entity->getState();
        }
        return $result;
    }

    /**
     * @param int $pin
     * @param int $state
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPinState(int $pin, int $state)
    {
        $pin = $this->findOneBy(['id' => $pin, 'status' => 1]);
        $pin->setMode($state);
        $this->getEntityManager()->persist($pin);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $pin
     * @return Pin|null
     */
    public function getPin(int $pin)
    {
        return $this->findOneBy(['pin' => $pin, 'status' => 1]);
    }

    // /**
    //  * @return Pin[] Returns an array of Pin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pin
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
