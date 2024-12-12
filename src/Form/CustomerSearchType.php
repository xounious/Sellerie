<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CustomerSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    $builder
        ->add('search', TextType::class, [
            'label' => 'Rechercher un client par son Nom',
            'required' => false,
        ])
        ->add('search', SubmitType::class, [
            'label' => 'Rechercher le client',
        ]);
    }
}
