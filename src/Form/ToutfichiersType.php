<?php

namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Memoires;
use App\Entity\Equipes;
use App\Entity\Totalequipes;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;

class ToutfichiersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        
      
        $builder
            // ...
          ->add('fichier', FileType::class, [
                                'label' => 'Choisir le fichier ',
                          'mapped' => false,

              
                'required' => false,
                
                     
              
              ])
              ->add('typefichier', ChoiceType::class, [
                            'mapped'=>false,
                            'required' => false,
                            'choices' => [
                                'Mémoire(pdf, 2,5 M max, 20 pages)'=>0,
                                'Annexe(pdf, 2,5 M max  20 pages)'=>1,
                                'Résumé(pdf, 1 M max, 1 page)'=>2,
                                'Fiche sécurité(1M max, doc, docx, pdf, jpg, odt)'=>4,
                                'Présentation((pdf, 10 M max)'=>3]
                               ] )
              ->add('save',      SubmitType::class);
                
           
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => null,
        ]);
    }
}