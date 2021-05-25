<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityGoogleController extends AbstractController
{

    /**
     * @Route ("/connect/google", name="google_connect")
     * @param ClientRegistry $clientRegistry
     */
    public function connect(ClientRegistry $clientRegistry)
    {
        /** @var GoogleClient $client */
        $client = $clientRegistry->getClient('google');
        $this->addFlash("succes", "Bienvenue, a présent complétez votre profil");
        return $client->redirect(['openid', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile']);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
