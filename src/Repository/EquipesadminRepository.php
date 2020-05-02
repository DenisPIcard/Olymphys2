<?php
namespace App\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Equipesadmin;
use App\Entity\Memoiresinter;
/**
 * EquipesadminRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EquipesadminRepository extends ServiceEntityRepository
{
    
   
  public function __construct(ManagerRegistry $registry)
                    {
                        parent::__construct($registry, Equipesadmin::class);
                    }
    
 public function getEquipeInter(EquipesadminRepository $er): QueryBuilder
                {   
		
                    return $er ->createQueryBuilder('e')->select('e');
                          
                             
                }
   public function getEquipeNa(EquipesadminRepository $er): QueryBuilder
                {   
		
                    return $er ->createQueryBuilder('e')->select('e')
                                      ->where('e.selectionnee= TRUE')
                                       ->orderBy('e.lettre','ASC');
                          
                             
                }
    public function getEquipesNatSansMemoire(EquipesadminRepository $er): QueryBuilder
    {
                 return $er ->createQueryBuilder('e')->select('e')
                                       ->where('e.selectionnee= TRUE')
                                       ->orderBy('e.lettre','ASC');
        
        
    }
                
       
}