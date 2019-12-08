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
    private $tokenManager;
    
        public function __construct(CsrfTokenManagerInterface $tokenManager = null)
    {
        $this->tokenManager = $tokenManager;
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     * 
     * @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(Request $request)
    {
        /** @var $session Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }
    
    /**
     * @Route("/check", name="check")
     */
    public function check()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    
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
