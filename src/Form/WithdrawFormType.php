<?php

namespace App\Form;

use App\Entity\CompteBancaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Doctrine\ORM\EntityRepository;

class WithdrawFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Obtenez l'utilisateur connecté depuis les options
        $user = $options['user'];

        $builder
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class,
                'choice_label' => 'numeroDeCompte',  // Affichez le numéro de compte
                'label' => 'Compte source',
                'placeholder' => 'Sélectionnez un compte',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    // Utilisez 'utilisateur' au lieu de 'user' pour le filtrage
                    return $er->createQueryBuilder('c')
                        ->where('c.utilisateur = :user')  // Utilisez 'utilisateur' ici
                        ->setParameter('user', $user);
                },
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant du retrait',
                'currency' => 'EUR',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null,  // L'utilisateur connecté sera passé ici
        ]);
    }
}
