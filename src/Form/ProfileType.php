<?php
//App/Form/ProfileType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class ProfileType extends AbstractType
{
    protected $translationDomain = 'App/translations'; //
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nom', null, ['label'=> 'Votre nom'])
                ->add('prenom', null, ['label'=> 'Votre prénom'])
                ->add('adresse', null, ['label'=>'Votre adresse (numéro +rue)'])
                ->add('ville', null, ['label'=>'Votre ville'])
                ->add('code', null, ['label'=>'Votre code'])
                ->add('phone', null, ['required'=> false, 'label'=>'Votre téléphone portable, si possible',])
                ->add('rne', null, ['required'=> false, 'label'=>'RNE, si vous comptez inscrire une équipe'])
         ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
    
    public function getBlockPrefix()
    {
        return 'user_registration';
    }
}


