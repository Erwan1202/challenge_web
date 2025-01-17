<?php

namespace App\Form;

use App\Entity\CompteBancaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TransferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from_account', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numero_de_compte',
                'label' => 'Compte source',
                'placeholder' => 'Sélectionnez un compte',
            ])
            ->add('to_account', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numero_de_compte',
                'label' => 'Compte destinataire',
                'placeholder' => 'Sélectionnez un compte',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Montant du virement',
                'currency' => 'EUR',
            ])
            ->add('label', TextType::class, [
                'label' => 'Description (optionnel)',
                'required' => false,
            ]);
    }
}
