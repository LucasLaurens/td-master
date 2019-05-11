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
     * @Route("/biens", name="property.index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response {
        // $properties = $this->repository->findAllVisible();
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


// $property = new Property();
        // $property->setTitle('Mon premier bien')
        //     ->setPrice(2000)
        //     ->setRooms(4)
        //     ->setBedrooms(3)
        //     ->setDescription('Nouvelle chambre de libre')
        //     ->setSurface(60)
        //     ->setFloor(4)
        //     ->setHeat(1)
        //     ->setCity('Annecy')
        //     ->setAdress('202 route des craix')
        //     ->setPostalCode('34000');
        // $em = $this->getDoctrine()->getManager();
        // $em->persist($property);
        // $em->flush();
        // $repository = $this->getDoctrine()->getRepository(Property::class);
        // dump($repository);
        // $property[0]->setSold(true);
        // $this->em->flush();