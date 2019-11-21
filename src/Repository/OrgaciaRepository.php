<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Orgacia;

/**
 * OrgaciaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below. Copier coller de visites 
 */
class OrgaciaRepository extends ServiceEntityRepository
{
     public function __construct(ManagerRegistry $registry)
                    {
                        parent::__construct($registry, Orgacia::class);
                    }
   
}
