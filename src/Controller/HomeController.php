<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * Description : on initialise le repository et la route lorsque l'on va ouvrir la page web
     * Ensuite on appelle la méthode findlatest qui se trouve dans le fichier Property Repository
     * puis on envoie nos propriétés a la vue
     * 
     * @Route("/", name="home")
     * @param PropertyRepository $repository
     * @return Response
     */
    public function index (PropertyRepository $repository): Response 
    {
        $properties = $repository->findLatest();
        return $this->render('pages/home.html.twig', [
            'properties' => $properties
        ]);
    }

}