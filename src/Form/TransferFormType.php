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
            // Change 'from_account' to 'compteSource'
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numeroDeCompte',
                'label' => 'Compte source',
                'placeholder' => 'Sélectionnez un compte',
            ])
            // Change 'to_account' to 'compteDestination'
            ->add('compteDestination', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numeroDeCompte',
                'label' => 'Compte destinataire',
                'placeholder' => 'Sélectionnez un compte',
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant du virement',
                'currency' => 'EUR',
            ]);
    }
}
