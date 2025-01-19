<?php

namespace App\Form;

use App\Entity\CompteBancaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WithdrawFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class, // Liaison avec l'entité CompteBancaire
                'choice_label' => 'numeroDeCompte', // Affichage du numéro de compte
                'label' => 'Compte source',
                'placeholder' => 'Sélectionnez un compte', // Ajout d'un placeholder pour guider l'utilisateur
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant à retirer',
                'currency' => 'EUR', // La devise en EUR
            ]);
    }
}
