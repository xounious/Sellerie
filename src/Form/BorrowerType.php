<?php

namespace App\Form;

use App\Entity\Borrower;
use App\Entity\Employee;
use App\Entity\BorrowerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BorrowerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityManager = $options['entityManager'];
    $builder
        ->add('borrowerType', ChoiceType::class, [
        'choices' => [
            'Client' => $entityManager->getRepository(BorrowerType::class)->findOneBy(['name' => 'customer'])->getId(),
            'Employé' => $entityManager->getRepository(BorrowerType::class)->findOneBy(['name' => 'employee'])->getId(),
        ],
        'expanded' => true,
        'multiple' => false,
        'label' => 'Quel est le type de l\'emprunteur ?',
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
        ->add('borrowerEmail', EmailType::class, [
        'label' => 'Borrower Email',
        'required' => false,
        ])
        ->add('clientName', TextType::class, [
        'label' => 'Client Name',
        'required' => false,
        ])
        ->add('clientEmail', EmailType::class, [
        'label' => 'Client Email',
        'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Borrower::class,
            'entityManager' => null,
        ]);
    }
}
