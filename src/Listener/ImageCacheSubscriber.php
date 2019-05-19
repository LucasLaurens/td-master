<?php 
namespace App\Listener;

use Doctrine\Common\EventSubscriber;
use App\Entity\Property;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCacheSubscriber implements EventSubscriber {

    /**
     * Description: pour ce qui est de l'implementation nous avons créé un nouveau service dans le Yaml pour que Symfony comprenne
     * on créer cette classe afin de gérer le cache des images 
     * On commence par injecter le cacheManager qui va intéragir avec le dossier properties (images) puis le UploaderHelper afin d'avoir l'ancienne image qui est l'imageFile qu'on va remplacer par la nouvelle
     * Puis on créer une fonction qui va retourner nos eventlistener
     * et on créer les fonctions correspondantent 
     * pour ce qui est de la preUpdate elle récupère notre entité image si elle n'est pas une instance de property alors l'action retourne rien pas de update (l'événement ne se lance pas) par contre 
     * si l'image récupérée est une instance de uplodedFile alors comme je le disais précédement on récupère l'ancienne image qu'on remove
     * Puis pour le preRemove c'est presque pareil sauf que si c'est lié à property alors on remove dans tous les cas 
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHealper
     */
    private $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args) 
    {
        $entity = $args->getEntity();
        if(!$entity instanceof Property) {
            return;
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args) 
    {
        $entity = $args->getEntity();
        if(!$entity instanceof Property) {
            return;
        }
        if($entity->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
        }  
    }



}