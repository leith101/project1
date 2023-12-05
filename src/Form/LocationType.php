<?php

namespace App\Form;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Correct import statement
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as DoctrineEntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;




class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('start_date')
        ->add('end_date')
        ->add('velo', EntityType::class, [
            'class' => 'App\Entity\Velo',
            'choice_label' => 'modele',
            'label' => 'Choose a Velo',
        ])
        ->add('prix', TextType::class, [
            'label' => 'prix',
            'mapped' => false,
            'required' => false, // Le champ n'est pas requis
            'attr' => [
                'readonly' => true,
                'disabled' => true, // Empêche la modification du champ
            ],
        ]);



    // Ajouter un écouteur d'événements pour mettre à jour la valeur du champ "prix"
    $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        $location = $event->getData();
        $form = $event->getForm();

        if ($location) {
            // Mettre à jour la valeur du champ "prix" en utilisant la méthode getVeloPrice
            $form->get('prix')->setData($location->getVeloPrice());
        }
    });
}

        
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'attr' => [
                'novalidate' => 'novalidate', // Désactive la validation HTML5 côté client pour le formulaire
            ],
        ]);
    }
}