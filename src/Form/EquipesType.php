<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 

use Symfony\Component\Form\AbstractType;

use App\Entity\Equipes ;
use App\Entity\Totalequipes ;
use App\Entity\Jures ;
use App\Entity\Notes ;
use App\Entity\Phrases ;
use App\Entity\Visites ;
use App\Entity\Cadeaux ;
use App\Entity\Liaison ;
use App\Entity\Prix ;
use App\Entity\Classement ;

use App\Form\PhrasesType ;
use App\Form\CadeauxType ;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextaeraType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->Modifier_Rang = $options['Modifier_Rang']; 
        $this->Attrib_Phrases = $options['Attrib_Phrases'];
        $this->Attrib_Cadeaux = $options['Attrib_Cadeaux'];
        $this->Deja_Attrib = $options['Deja_Attrib'];


        if($options['Modifier_Rang'])
        {
        $builder
            ->add('rang', IntegerType::class) // au lieu de TextType
            ->add('Enregistrer', SubmitType::class);                        
        }
        
        elseif($options['Attrib_Phrases'])
        {
        $builder
            ->add('phrases', PhrasesType::class)
            ->add('liaison', EntityType::class, [
                    'class' => 'App:Liaison',
                    'choice_label'=> 'getLiaison',
                    'multiple' => false])
            ->add('Enregistrer', SubmitType::class);            
        }

        elseif($options['Attrib_Cadeaux']) 
        {
            if($options['Deja_Attrib'])
            {
            $builder
                ->add('cadeau', CadeauxType::class)
                ->add('Enregistrer', SubmitType::class);
            }
            else
            {
            $builder
                ->add('cadeau', EntityType::class, [
                    'class' => 'App:Cadeaux', 
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                        ->where('c.attribue = 0')
                        ->orderBy('c.montant', 'DESC');
                    },
                    'choice_label'=> 'displayCadeau', 
                    'multiple' => false])
                ->add('Enregistrer', SubmitType::class);

            }
        }

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Equipes',
            'Modifier_Rang'=>false,
            'Attrib_Phrases' =>false, 
            'Attrib_Cadeaux'=>false,
            'Deja_Attrib'=>false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cyberjury_equipes';
    }


}
