<?php
// src/Controller/CoreController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CoreController extends AbstractController
{
    /**
     * @Route("/", name="core_home")
     */
  public function index(SessionInterface $session)
  {
    $centre='';
    $user=$this->getUser();
    if (null != $user)
    {
     
     
     $session->start();
     $session->set('user', $user);
    
     $roles=$user->getRoles();
    foreach($roles as $role){
        if ($role=='ROLE_ORGACIA'){
            $centre=$user->getCentrecia()->getCentre();
        }
     
    }
    
    
    }
    return $this->render('core/index.html.twig',array('centre'=>$centre ));
  }
}
