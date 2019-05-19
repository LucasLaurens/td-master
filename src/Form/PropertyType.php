<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Option;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PropertyType extends AbstractType
{
    /**
     * Description: creation d'un form grace à l'extends de la class AbstractType qui prédéfinit les params
     * J'ajoute tous les champs de mon bien 
     * avec pour heat : une methode qui recupere la constante que j ai créée dans Property avec un tableau à deux possibilités et je boucle dessus en expliquant que dans mon nouveau tableau vide je vais position les valeurs à la place des clés afin d'afficher les valeurs de ce nouveau tableau 
     * On a aussi ajouté un champ Options avec le EntityType(qui designe le choicetype mais pour les options (lorsqu'il y en a plusieurs)) à qui on passe la classes Option, le required à false pour que lors de la recherche le champ ne soit pas obligatoire
     * On ajoute le champ imageFile qui est du type File car on post une image et qui n'est pas obligatoire pour le form 
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat', ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true
            ])
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
            ->add('city')
            ->add('adress')
            ->add('postal_code')
            ->add('sold')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices() 
    {
        $choices = Property::HEAT;
        $output = [];
        foreach($choices as $k => $v)  {
            $output[$v] = $k;
        }
        return $output;
    }

}
