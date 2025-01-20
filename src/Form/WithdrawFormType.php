<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\CompteBancaire;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class WithdrawFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Vérifiez que l'utilisateur est passé comme option
        if (!array_key_exists('user', $options) || !$options['user']) {
            throw new \InvalidArgumentException('L\'option "user" est obligatoire et doit être définie.');
        }

        // $user = $options['user'];

        // Construire le formulaire
        $builder
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.utilisateur = :user')  // Filtre les comptes de l'utilisateur connecté
                        ->setParameter('user', $options['user']);
                },
                'choice_label' => 'numeroDeCompte', // Affichez le numéro de compte
                'label' => 'Compte source',
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant du retrait',
                'currency' => 'EUR',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Effectuer le retrait',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class, // Lier le formulaire à l'entité Transaction
            'user' => null,  // L'utilisateur connecté sera passé ici
        ]);

        // Définir l'option "user" comme obligatoire
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', ['App\Entity\Utilisateur', 'null']);
    }
}
