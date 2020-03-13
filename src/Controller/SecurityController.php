<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserRegistrationFormType;
use App\Form\ProfileType;
use App\Form\ResettingType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Mime\Address;


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
    public function register(MailerInterface $mailer, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, TokenGeneratorInterface $tokenGenerator)
    {

        $form = $this->createForm(UserRegistrationFormType::class );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['plainPassword']->getData()
            ));
            $user->setIsActive(0);
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            // Enregistre le membre en base
            // be absolutely sure they agree
            if (true === $form['agreeTerms']->getData()) {
                $user->agreeTerms();
            }
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($user);
            $em->flush();
            $email=(new TemplatedEmail())
                    ->from(new Address('info@olymphys.fr','Équipe Olymphys'))
                    ->to(new Address($user->getEmail(), $user->getNom()))
                    ->subject('Bienvenue sur Olymphys')
                    ->htmlTemplate('email/bienvenue.html.twig')
                    ->context([
                        'user' => $user,
                        ]);
            $mailer->send($email);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez finaliser votre inscription. Le lien que vous recevrez sera valide 24h.");        
            return $this->redirectToRoute("core_home");
        }
        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
        
     }
     
         // si supérieur à 24h, retourne false
    // sinon retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;        
        }
        
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 60 * 24;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }
    
    /**
     * @Route("/verif_mail/{id}/{token}", name="verif_mail")
     */
    public function verifMail(User $user, Request $request, MailerInterface $mailer, string $token)
    {
 
       // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 24h
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            dd($user->getToken(),$token,$this->isRequestInTime($user->getPasswordRequestedAt()));
            throw new AccessDeniedException();
        }

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setIsActive(1);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $email=(new TemplatedEmail())
                    ->from(new Address('info@olymphys.fr','Équipe Olymphys'))
                    ->to(new Address('webmestre2@olymphys.fr', 'Denis'))
                    ->subject('Nouvel utilisateur sur Olymphys')
                    ->htmlTemplate('email/nouvel_utilisateur.html.twig')
                    ->context([
                        'user' => $user,
                        ]);
            $mailer->send($email);
            $request->getSession()->getFlashBag()->add('success', "Votre inscription est terminée, vous pouvez vous connecter.");

            return $this->redirectToRoute('login');

        
    }
    
             /**
     * @Route("/forgottenPassword", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {

        // création d'un formulaire "à la volée", afin que l'internaute puisse renseigner son mail
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findOneByEmail($form->getData()['email']);

            // aucun email associé à ce compte.
            if (!$user) {
                $request->getSession()->getFlashBag()->add('warning', "Cet email ne correspond pas à un compte.");
                return $this->redirectToRoute("forgotten_password");
            } 

            // création du token
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $em->flush();

            $email=(new TemplatedEmail())
                    ->from(new Address('info@olymphys.fr','Équipe Olymphys'))
                    ->to(new Address($user->getEmail(), $user->getNom()))
                    ->subject('Renouvellement du mot de passe')
                    ->htmlTemplate('email/password_mail.html.twig')
                    ->context([
                        'user' => $user,
                        ]);
            $mailer->send($email);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez renouveler votre mot de passe. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("core_home");
        }

        return $this->render('security/password_request.html.twig', [
            'passwordRequestForm' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/reset_password/{id}/{token}", name="reset_password")
     */
    public function resetPassword(User $user, Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {
 
       // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['plainPassword']->getData()));
            $plainPassword = $form->getData();

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

            return $this->redirectToRoute('login');

        }

        return $this->render('security/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView()
        ]);
    }
            /**
     * @Route("/profile_show", name="profile_show")
     */
    public function profileShow()
    {
        $user = $this->getUser();
        return $this->render('profile/show.html.twig', array(
            'user' => $user,
        ));
    }
    
    /**
     * Edit the user.
     *
     * @param Request $request
     * @Route("profile_edit", name="profile_edit")
     */
    public function profileEdit(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('core_home');
        }

        return $this->render('profile/edit.html.twig', array(
            'editProfileForm' => $form->createView(),
            'user' => $user,
        ));
    }

}
