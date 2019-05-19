<?php 
namespace App\Notification;

use App\Entity\Contact;
use Twig\Environment;

class ContactNotification {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    /**
     * Description: On fait appel au Swift_Mailer pour l'envoi d'email et à l'environnement pour le rendu HTML 
     * ensuite avec la function notify on set le message : 
     * On récupère la propriété donc le bien et son titre pour le reconnaître 
     * ensuite on set d'où le mail vient, à qui il va-t-il être envoyé et à qui on va devoir répondre 
     * puis le body qui est une view que l'on doit créer (contact.html.twig)
     * Et on envoie le mail avec la méthode send
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer) 
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }
    

    public function notify(Contact $contact) {
        $message = (new \Swift_Message('Agence : ' . $contact->getProperty()->getTitle()))
            ->setFrom('noreply@agence.fr')
            ->setTo('contact@agence.fr')
            ->setReplyTo($contact->getEmail())
            ->setBody($this->renderer->render('emails/contact.html.twig', [
                'contact' => $contact
            ]), 'text/html');
            $this->mailer->send($message);
    }
}