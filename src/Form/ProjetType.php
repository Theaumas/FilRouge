<?php

namespace App\Form;

use App\Entity\Projets;
use App\Entity\User;
use App\Enum\ProjetStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom du projet : ',
                'attr' => ['placeholder' => 'Entrez le nom du projet',],
            ])
            ->add('description', null, [
                'label' => 'Description : ',
                'attr' => [
                    'placeholder' => 'Décrivez le projet',
                ],
                'required' => false, 
            ])
            ->add('dateLimite', DateType::class, [
                'label' => 'Date limite : ',
                'widget' => 'single_text',
                'attr' => [ 'placeholder' => 'Choisissez une date limite pour le projet',],
                'required' => false,
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut : ',
                'choices' => ProjetStatus::cases(),
                'choice_label' => fn(ProjetStatus $status) => $status->label(),
                'choice_value' => fn(?ProjetStatus $status) => $status?->value,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projets::class,
        ]);
    }
}
