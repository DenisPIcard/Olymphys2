<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * CadeauxRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below. Copier coller de visites 
 */
class CadeauxRepository extends \Doctrine\ORM\EntityRepository
{
    public function getListCadeaux()
    {
       $query = $this->createQueryBuilder('c')
                     ->orderBy('c.montant', 'DESC')
                     ->getQuery();
       return $query ->getResult();

    }
     public static function getCadeaux(CadeauxRepository $cr): QueryBuilder
                {   
		
                    return $cr->createQueryBuilder('c')->select('c');
                            //->where('c.attribue = :attribue')
                            //->setParameter('attribue',0);    
                    
 
                     }
    
    
}

