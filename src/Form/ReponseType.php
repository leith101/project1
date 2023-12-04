<?php
namespace App\Form;

use App\Entity\Reponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description_reponse', TextareaType::class, [
                // ... (votre configuration existante)
            ])
            ->add('reclamation', EntityType::class, [
                'class' => 'App\Entity\Reclamation',
                'choice_label' => function ($reclamation) {
                    return sprintf(
                        'Réclamation #%d - État : %s - Description : %s - Date de création : %s',
                        $reclamation->getIdReclamation(),
                        $reclamation->getEtatReclamation() ? 'Traitée' : 'Non traitée',
                        $reclamation->getDescriptionReclamation(),
                        $reclamation->getDateReclamation()->format('Y-m-d H:i:s')
                    );
                },
                'attr' => [
                    'class' => 'form-select', // Ajoutez une classe CSS pour styliser le sélecteur de réclamation
                ],    'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.id_reclamation', 'ASC');
                },
                // ... (autres options du formulaire)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}