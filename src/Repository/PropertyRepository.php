<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use App\Entity\PropertySearch;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * Description: Injection de la class PropertySearch qui va chercher toutes les options
     * on retourne une query car on va avoir besoin de voir les biens en base de données
     * plus appelle de la méthode findvisiblequery afin de récupérer tous les liens non vendus
     * Ensuite on a créé une condition qui dit : si la recherche est faite sur le getter maxprice alors la requête sera fait pour cette option-ci de même pour la min Surface
     *  Vu que l'on a appelé la class PropertySeacrh et Property on peut appeler les biens de la bdd et mettre une condition où l'on donne des paramettre que le set avec les méthodes créé dans le property Search
     * 
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search): Query
    {
        $query = $this->findVisibleQuery();
        
        if($search->getMaxPrice()){
            $query = $query
                ->andWhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        if($search->getMinSurface()){
            $query = $query
                ->andWhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }
        // On ajoute une condition si la recherche récupère des options (donc au moins une) elle fait la requête où l'option numéro x (: option$k) qui est le paramètre passé à la requête
        // on crée une boucle sur toutes les options et dans la requête sur le paramètre on passe les options qui ont été trouvé.
        if($search->getOptions()->count() > 0){
            $k = 0;
            foreach($search->getOptions() as $option) {
                $k++;
                $query = $query
                ->andWhere(':option$k MEMBER of p.options')
                ->setParameter('option$k', $option);
            }
        }
        
        return $query->getQuery();
    }

    /**
     * Description : on explique que cette méthode va nous renvoyer un tableau de propriété puis on appelle la méthode findVisibleQuery
     * et dit que veulent les 4 derniers résultats et on fait notre requête
     * 
     * @return Property[]
     */
    public function findLatest(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    /**
     * Description : on créer une méthode qui permet de récupérer toutes les propriétés qui ne sont pas vendues dans la base de données
     */
    private function findVisibleQuery (): QueryBuilder 
    {
        return $this->createQueryBuilder('p')
        ->where('p.sold = false');
    }
}
