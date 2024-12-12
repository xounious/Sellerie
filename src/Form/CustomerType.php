<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    $builder
        ->add('search', TextType::class, [
            'label' => 'Rechercher un client',
            'required' => false,
        ])
        ->add('phone', NumberType::class, [
        'label' => 'téléphone',
        ])
        ->add('firstname', TextType::class, [
        'label' => 'Prénom',
        'required' => false,
        ])
        ->add('lastname', TextType::class, [
        'label' => 'Nom',
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Créer le client',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
