<?php

namespace App\Form;

use App\Entity\Addresse;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,[
                'label'=> 'Titre',
                'attr'=> ['placeholder' => 'Titre de l\'adresse'],
                'help' => 'Exemple : Domicile, Bureau, Autre',
            ])
            ->add('adresse', TextType::class,[
                'label'=> 'Adresse',
                'attr'=> ['placeholder' => 'Votre adresse']
            ])
            ->add('Ville', TextType::class,[
                'label' => 'Ville', 
                'attr' => ['placeholder' => 'Votre Ville']
            ])
            ->add('CodePostal', TextType::class, [
                'label'=> 'Code Postal', 
                'attr'=> ['placeholder'=> 'Le Code Postal de votre Ville']
            ])
            ->add('Pays', CountryType::class, [
                'label'=> 'Pays', 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresse::class,
        ]);
    }
}
