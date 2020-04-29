<?php

namespace App\Form\Filter;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\FilterTypeTrait;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Edition ;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;



class EquipeFilterType extends FilterType
{ use FilterTypeTrait;
    
    public function filter(QueryBuilder $queryBuilder, FormInterface $form, array $metadata)
    { 
        
       $data =$form->getData();
     
       if($data instanceof Edition){
          
          
         $queryBuilder->Where('entity.edition =:edition')
                              ->setParameter('edition',$data);
        
       }
       if($data instanceof Centrescia){
          
          if (null!==$form->getData()){
         $queryBuilder->leftJoin('entity.centre','c')
                                ->Where('centre =:centre')
                                ->setParameter('centre',$data);
          }
          
              
       }

        // ...
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



