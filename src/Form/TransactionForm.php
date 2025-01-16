<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Dépôt' => 'deposit',
                    'Retrait' => 'withdrawal',
                    'Virement' => 'transfer',
                ],
                'label' => 'Type de transaction',
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Montant',
            ])
            ->add('compte_source', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numero',
                'label' => 'Compte source',
                'required' => false, // Pas nécessaire pour les dépôts
            ])
            ->add('compte_destination', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numero',
                'label' => 'Compte destination',
                'required' => false, // Pas nécessaire pour les retraits
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
