<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    /**
     * Description: Vu que l'on a initialisé la route dans le .yaml on peut l'appeler ici 
     * grace au composant AuthenticationUtils on peut récupérer l'username rentré et les erreurs s'il y en a (qu'on envoie ensuite à la vue)
     * 
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils) 
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastusername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastusername,
            'error' => $error
        ]);
    }

}