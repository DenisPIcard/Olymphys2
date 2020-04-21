<?php
// src/Controller/CoreController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Useranc;

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
       /* if ($role=='ROLE_ORGACIA'){
            $centre=$user->getCentrecia()->getCentre();
        }
     */
        $centre = '';
    }
    
    
    }
    return $this->render('core/index.html.twig',array('centre'=>$centre ));
  }
  
      /**
     * @Route("mod_user", name="mod_user")
     */
  public function mod_user() {
      $repositoryAutre=$this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Autre');
      $listAutre= $repositoryAutre->findAll();
      $em=$this->getDoctrine()->getManager();
      foreach($listAutre as $useranc) {
          $role = $useranc->getRoles_anc();
          $useranc->setRoles($role);
          $em->persist($useranc);
         // dd($useranc);
        }
      
      $em->flush();
  }
}
