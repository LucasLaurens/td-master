<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /**
     * Description: avec notre invité de commande on creer une fixtures qui permet de creer facilement un fakeUser 
     * Pour on initialise le component UserPasswordEncoderInterface en l'injectant dans notre constructeur pour qu'il utilise l'encodage précisé dans le service.yaml
     * Pour finir on créer un nouveau user et on set les données puis on les envois dans la base de données et on on load dans l'invit de command afin que le user se rajoute
     * 
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('demo');
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $manager->persist($user);
        $manager->flush();
    }
}
