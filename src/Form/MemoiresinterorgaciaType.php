<?php

namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Memoiresinter;

use App\Entity\Equipesadmin;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;

class MemoiresinterorgaciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        
        
        
        
       
    
       
        $builder
            // ...
         ->add('Numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => 'App\Repository\EquipesadminRepository::getEquipes',
                                       'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe .',
                                       'mapped'=>false,
                                         ])     
                ->add('memoire', FileType::class, [
                                'label' => 'Choisir le mémoire de votre équipe  (de type PDF de taille inférieure à 2,5 M )',
                          'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,
                 
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2600k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Attention, votre mémoire ne correspond pas au format imposé !',
                    ])
                ],      
              
              ])
              ->add('annexe', CheckboxType::class,['label'=>'Cliquez ici pour une annexe' , 'required' =>false, 'mapped' => false])
              ->add('Deposer ce memoire',      SubmitType::class);
            //->add('lettre',EntityType::class,[               'class' =>'App:Equipes',               'choice_label'=>'getlettre',     'multiple' => false ]) // ...
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Memoiresinter::class,
        ]);
    }
}