<?php

namespace App\DataFixtures;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 
use Doctrine\ORM\QueryBuilder;
use App\Entity\User;
use App\Entity\Jures;
use App\Repository\JuresRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixetures extends Fixture
{
     private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {  
        $JuresRepository=new JuresRepository;
        $Jures=$JuresRepository->getAll();
        foreach($jure as $Jures){        
        
            $user = new User();
            $user->setUsername($jure->getNomJure());
            $user->setRoles(['ROLE_JURE']);
            $user->setEmail($jure->getNomJure().'@olymp.fr');
            //$user->setPassword($jure->getPrenomJure());
            $user->setPassword($this->passwordEncoder->encodePassword($user,$jure->getPrenomJure() ));
             $manager->persist($user);

            // add more products

            $manager->flush();
        }
    }
}

