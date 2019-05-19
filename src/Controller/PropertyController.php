<?php
namespace App\Controller;

use App\Entity\Property;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Form\ContactType;
use App\Entity\Contact;
use App\Notification\ContactNotification;

class PropertyController extends AbstractController{

    /**
     * Description: on initialise le repository
     * 
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var Property
     */
    private $property;

    public function __construct (PropertyRepository $repository, ObjectManager $em) {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * Description: On injecte le PaginatorInterface pour appliquer la pagination via le bundle knp que l'on a téléchargé via composer
     * on crée la méthode paginate à laquelle on envoie la méthode afin de trouver tous les biens et on envoie la requête (que l'on injecte aussi) 
     * pour dire que l'on veut les pages et si aucune n'est demandé par défaut ça sera la page 1 et on dit que par défaut il faut 12 biens par page
     * puis on l'envoie a la vue index
     * on créer également une nouvelle recherche via le PropertySearch puis on créer un form via PropertySearchType à qui on donne la recherche en paramètre
     * et ensuite on fait une requête et on l'envoie à la vue
     * 
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()
        ]);
    }

    /**
     * Description : après avoir créé la méthode getter getSlug on définit une route avec un nom que l'on va réutiliser dans la vue et un paramètre regex qui dit que l'on peut avoir certains caractères
     * Ensuite si le slug est différent de celui renvoyé alors on le redirige sur le bon slug (et on lui renvoie le status)
     * En ce qui concerne l'email on créer un nouveau formulaire de contact grâce à la class on set la property pour connaître le bien sur lequel on se trouve lors de l'envoi d'email
     * On créer le form par rapport à la class ContactType où l'on a initialiser les champs qu'on envoie ensuite à la view
     * On dit au form de gérer la requête et si celle-ci donc si le form est submit et valid (avec tous les critères respectés (pas d'erreur))
     * alors on fait appel à la class ContactNotification afin de gérer l'envoi de notre message avec son body sous format HTML 
     * Le message de succès et la redirection vers ce bien avec l'id et le slug
     * 
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @return Response
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response {
        
        if($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre message à bien été envoyé');
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties',
            'form' => $form->createView()
        ]);
    }

}