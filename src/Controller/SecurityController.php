<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('Sera intercepté avant d\'en arriver là !');
    }
   
    protected function renderLogin(array $data)
    {
        return $this->render('security/login.html.twig', $data);
    }
    
    /**
     * @Route("/register", name="register")
     */
    public function register(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {

        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['plainPassword']->getData()
            ));
          
            // be absolutely sure they agree
            if (true === $form['agreeTerms']->getData()) {
                $user->agreeTerms();
            }
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($user);
            $em->flush();
            
            $email = (new TemplatedEmail())
                ->from(new Address('alienmailcarrier@example.com', 'The Space Bar'))
                ->to(new Address($user->getEmail(), $user->getNom()))
                ->subject('Welcome to the Space Bar!')
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    'user' => $user,
                    ]);
            $mailer->send($email);
                    
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
        
     }
}
