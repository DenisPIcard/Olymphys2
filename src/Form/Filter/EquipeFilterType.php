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
        
       $datas =$form->getParent()->getData();
       
      
       
      if(isset($datas['edition'])){
            
         $queryBuilder->Where( 'entity.edition=:edition')
                              ->setParameter('edition',$datas['edition']);
       }     
       if(isset($datas['centre'])){
                    
           $queryBuilder->andWhere( 'entity.edition=:centre')
                              ->setParameter('centre',$datas['centre']);
           
       }
      /* $listparam=[];
        if(isset($datas['edition'])){
            
         $queryBuilder->join('entity.edition','e')
                               ->Where( 'e.edition=:edition');
                              $listparam['edition']=$datas['edition'];
       }     
         if(isset($datas['centre'])){
                             $centres = $datas['centre'];
                              $n=0;
                            foreach($centres as $centre){
                                $listparam['centre'.$n]=$centre;
                               
                              $queryBuilder->join('entity.centre','c'.$n)
                                      ->andWhere('c'.$n.'.centre =:centre'.$n);
                           $n++;} 
                                                      
                                $queryBuilder->setParameters($listparam);
         }*/
    }
    
     public function configureOptions(OptionsResolver $resolver)
    {    $resolver->setDefaults([
            'choice_label' => [
                'Edition' => 'edition',
                'Centre' => 'centre',
                // ...
            ],
        ]);
       
    }

    public function getParent()
    {
        return EntityType::class;
    }

   
}



