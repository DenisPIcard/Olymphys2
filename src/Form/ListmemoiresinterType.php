<?php
namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Memoiresinter;
use App\Entity\Equipesadmin;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;

class ListmemoiresinterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        
      
        $builder
            // ...
              ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
              ->add('equipe', EntityType::class,
                      [ 'class' => 'App:EquipesAdmin',
                                       // 'query_builder' => ,
                                        'choice_label'=> 'getTitreProjet',
                                        'multiple' => false, 
                                        'disabled'=>true,
                                        'label'=>false, 
                                        'expanded'=>false
                                     ]
                        )
              ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
              ->add('save',      SubmitType::class);
              
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