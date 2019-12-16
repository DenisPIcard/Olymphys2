<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController extends AbstractController
{
  
   
     /**
     * @Route("/login", name="login",)
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

       /* if($this->getUser()) {
         $this->redirectToRoute('core_home');
       }*/


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
  
    ///**
     //* @Route("/check", name="check")
    // */
    //public function check()
   // {
    //    throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
   // }
    
 
    

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('core_home');
    }
    protected function renderLogin(array $data)
    {
        return $this->render('security/login.html.twig', $data);
    }
}
