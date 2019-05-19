<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\PropertyRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PropertyType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Option;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\RouterCacheWarmer;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\PropertySearch;


// Creation du CRUD  
class AdminPropertyController extends AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * Description: Initialisation du repository et du manager afin d'interagir avec la base de données
     */
    public function __construct( PropertyRepository $repository, ObjectManager $em )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * Description: Création d'une root pour l'espace Admin + injection de la classe Request et Paginator
     * Appel de toutes les propriétés grâce à la fonction findAllVisibleQuery créé dans le repository et affichage d'une pagination avec 12 biens par pages
     * 
     * 
     * @Route("/admin", name="admin.property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request) 
    {
        $search = new PropertySearch();
        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * Description: la fonction new est un peu près équivalente à edit sauf pour certains détails comme le fait qu'on l'a persist pour dire que c'est une entité qui n'existe pas et qu'on va créer
     * avec de plus l'envoi d'un message flash (de succès)
     * 
     * @Route("/admin/property/create", name="admin.property.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request) {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * Description: Création d'une route pour l'édition 
     * on crée une nouvelle propriété pour cela on appelle la class property 
     * on instancie un formulaire à partir d'une Classe créée au préalable puis on envoie ce formulaire à la vue en expliquant qu'il doit être créé
     * on inject la requête pour ensuite vérifier si elle est envoyée et valide. Si c'est le cas on envoie les informations à la base donnée
     * puis redirection vers notre vue 
     * On a aussi précisé que l'on accepte que les méthodes GET et POST pour de pas confondre avec le delete
     * 
     * @Route("/admin/property/{id}", name="admin.property.edit", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function edit(Property $property, Request $request) 
    {

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * Description: Nous avons précisé que nous n'acceptions que les methods DELETE (j'ai donc créé un formulaire pour cette méthode dans la vue)
     * Suite à cela j'ajoute une condition qui dit que si le token utilise le template delete ou l'id de la propriété est exécuté et que le token est bien récupéré alors on effectue la suite
     * Ensuite on va utiliser la method remove afin de supprimer la propriété que l'on souhaite puis on fait un coup de flush pour envoyer les informations à la bdd
     * Avec un message de succès et la redirection vers la page admin
     * si le token n'est pas valide il ne supprime pas le bien et redirige vers l'admin
     * 
     * 
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Property $property, Request $request) 
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('admin.property.index');
    }



}