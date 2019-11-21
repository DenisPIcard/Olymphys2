<?php


namespace App\Form;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Photosinter;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;
use Vich\UploaderBundle\Form\Type\VichFileType;;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\TypeEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\File;

class PhotosinterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
      
        $builder->add('equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                       'query_builder'=>function (EntityRepository $ea) {
                                                        return $ea->createQueryBuilder('e')
                                                         ->orderBy('e.centre', 'ASC');},
                                        'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe .',
                                         'mapped'=>false
                                         ])
                                      ->add('photoFiles', FileType::class, [
                                      'label' => 'Choisir les photos(format .jpeg)',
                                        'mapped' => false,
                                       'required' => false,
                                        'multiple'=>true,
                                          ])
                                     ->add('Valider', SubmitType::class);
        
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