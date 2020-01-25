<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use App\Entity\Equipes ;
use App\Entity\Jures ;
use App\Entity\Phrases ;
use App\Entity\Visites ;
use App\Entity\Cadeaux ;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextaeraType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JuresType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    $this->list=$options['list'];

    foreach ($options['list'] as $key => $value)
    {
        $id=$key;
        $choix[$key]=$value;
    }
        $builder
        ->add('id', ChoiceType::class,array('choices' => $choix))
        ->add('Connexion', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Jures',
            'list' => array(1=>'Comite'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cyberjury_jures';
    }


}
