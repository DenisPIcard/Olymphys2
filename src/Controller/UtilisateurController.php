<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer;
use App\Form\UserType;
use App\Form\ResettingType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Mailer\MailerInterface;


class UtilisateurController extends AbstractController
{
    
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
 
        // création du formulaire
        $user = new User();
        // instancie le formulaire avec les contraintes par défaut, + la contrainte registration pour que la saisie du mot de passe soit obligatoire
        $form = $this->createForm(UserType::class, $user,[
           'validation_groups' => array('User', 'registration'),
        ]);        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
 
            // Encode le mot de passe
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            //inactive l'User en attente de la vérification du mail
            $user->setIsActive(0);
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            // Enregistre le membre en base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user); 
            $em->flush();

            $bodyMail = $mailer->createBodyMail('register/verif_mail.html.twig', [
                'user' => $user ]);
            $mailer->sendMessage('info@olymphys.fr', $user->getEmail(), 'Vérification de l\'adresse mail', $bodyMail);//'info@olymphys.fr'
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez finaliser votre inscription. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("core_home");

        }
        return $this->render('register/register.html.twig',
            array('form' => $form->createView())
        );
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
    public function verifMail(User $user, Request $request, Mailer $mailer, string $token)
    {
 
       // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 24h
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedHttpException();
        }

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);
            $user->setIsActive(1);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $bodyMail = $mailer->createBodyMail('register/mail_nouvel_user.html.twig', ['user' => $user]);
            //$mailer->sendMessage('info@olymphys.fr', 'webmestre2@olymphys.fr', 'Inscription d\'un nouvel utilisateur', $bodyMail);
            $mailer->sendMessage('info@olymphys.fr','info@olymphys.fr', 'Inscription d\'un nouvel utilisateur', $bodyMail);
            $request->getSession()->getFlashBag()->add('success', "Votre inscription est terminée, vous pouvez vous connecter.");

            return $this->redirectToRoute('login');

        
    }
    
         /**
     * @Route("/forgottenPassword", name="forgotten_password")
     */
    public function forgottenPassword(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
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

            // on utilise le service Mailer créé précédemment
            $bodyMail = $mailer->createBodyMail('password/mail.html.twig', [
                'user' => $user
            ]);
            $mailer->sendMessage('info@olymphys.fr', $user->getEmail(), 'Renouvellement du mot de passe', $bodyMail);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé afin que vous puissiez renouveler votre mot de passe. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("core_home");
        }

        return $this->render('password/request.html.twig', [
            'form' => $form->createView()
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
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

            return $this->redirectToRoute('login');

        }

        return $this->render('password/index.html.twig', [
            'form' => $form->createView()
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
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
}