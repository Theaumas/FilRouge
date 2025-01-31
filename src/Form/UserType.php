<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule', TextType::class,[
                'label' => 'Matricule:',
                'attr' => [ 'placeholder' => 'Votre Matricule de référence.']
            ])
            ->add('Prenom', TextType::class,[
                'label' => 'Prénom:',
                'attr' => ['placeholder' => 'Votre Prénom']
            ])
            ->add('Nom', TextType::class,[
                'label' => 'Nom:',
                'attr' => ['placeholder' => 'Votre Nom']
            ])
            ->add('DateDeNaissance', DateType::class, [
                'label' => 'Date de Naissance : '
            ])
            ->add('Tel', TelType::class, [
                'label' => 'Téléphone:',
                'attr' => ['placeholder' => 'Votre Numéro de Téléphone']
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo profil:',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
            ])

            ->add('service', TextType::class,[
                'label' => 'Service:',
                'attr' => ['placeholder' => 'Votre Service']
            ])
            
            ->add('email', EmailType::class,[
                'label' => 'Email:',
                'attr' => ['placeholder' => 'Votre Adresse Electronique valide']
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique',
                'label' => 'Mot De Passe: ', 
                'required' => true, 
                'first_options' => [
                    'label' => 'Mot de Passe: ',
                    'attr' => [ 'placeholder' => 'Mot de Passe de Génie']
                ],
                'second_options' => [
                    'label' => 'Mot de Passe: ',
                    'attr' => [ 'placeholder' => 'Confirmer votre Mot de Passe de Génie']
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
