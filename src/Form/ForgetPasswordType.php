<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Electronqiue', 
                'help' => 'Vous recevez votre nouveau mot de passe par email',
                'attr' => [
                    'placeholder' => 'email@exemple.fr'
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Réinitialiser',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Configure your form options here
        $resolver->setDefaults([
            
        ]);
    }
}
