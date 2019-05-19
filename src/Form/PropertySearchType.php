<?php

namespace App\Form;

use App\Entity\PropertySearch;
use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PropertySearchType extends AbstractType
{
    /**
     * Description: on Créer le formulaire de recherche 
     * on set les champs et on explique que les deux premiers sont de type integer (nombres) qu'ils ne sont pas obligatoires et qu'ils ont un placeholder
     * On a aussi ajouté un champ Options avec le EntityType(qui designe le choicetype mais pour les options (lorsqu'il y en a plusieurs)) à qui on passe la class Option, le required à false pour que lors de la recherche le champ ne soit pas obligatoire et multiple à true car plusieurs options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Budget maximal'
                ]
            ])
            ->add('minSurface', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Surface minimal'
                ]
            ])
            ->add('options', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
        ;
    }

    /**
     * Description: la methode est donc get car on cherche à récupérer des données et il n'y a pas besoin de token donc on le passe à false 
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
            'method' => 'get',
            'crsf_protection' => false
        ]);
    }

    /**
     * Description: on return un prefix vide afin d'avoir une requete plus courte et compréhensible dans la barre d'url
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
