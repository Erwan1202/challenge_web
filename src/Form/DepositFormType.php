<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Vérifiez que l'utilisateur est passé comme option
        if (!array_key_exists('user', $options) || !$options['user']) {
            throw new \InvalidArgumentException('L\'option "user" est obligatoire et doit être définie.');
        }

        $builder
        ->add('compteSource', EntityType::class, [
            'class' => CompteBancaire::class,
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('cb')
                    ->where('cb.utilisateur = :user')
                    ->setParameter('user', $options['user']);
            },
            'choice_label' => 'numeroDeCompte',
            'label' => 'Compte sur lequel effectuer le dépôt',
        ])
        ->add('montant', MoneyType::class, [
            'currency' => 'EUR',
            'label' => 'Montant à déposer',
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Effectuer le dépôt',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class, // Lier le formulaire à l'entité Transaction
            'user' => null, // Option obligatoire pour passer l'utilisateur
        ]);

        // Définir 'user' comme option requise
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', ['App\Entity\Utilisateur', 'null']);
    }
}
