<?php
/**
 * Created by PhpStorm.
 * User: leszek
 * Date: 13.05.19
 * Time: 22:14
 */

namespace App\Consumer;


use Doctrine\ORM\EntityManager;

abstract class ConsumerAbstract
{
    public function check(EntityManager $entityManager){
        if($entityManager->getConnection()->ping() == false){
            $entityManager->getConnection()->connect();
        }
    }
}