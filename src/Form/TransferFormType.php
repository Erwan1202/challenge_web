<?php

namespace App\Form;

use App\Entity\CompteBancaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

class TransferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté
        $user = $options['user'];

        $builder
            ->add('compteSource', EntityType::class, [  // Remplacez 'from_account' par 'compteSource'
                'class' => CompteBancaire::class,
                'choice_label' => 'numeroDeCompte',  // Affiche le numéro de compte
                'label' => 'Compte source',
                'placeholder' => 'Sélectionnez un compte',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    // Filtrer pour n'afficher que les comptes appartenant à l'utilisateur
                    return $er->createQueryBuilder('c')
                        ->where('c.utilisateur = :user')  // Filtrage sur l'utilisateur connecté
                        ->setParameter('user', $user);
                },
            ])
            ->add('compteDestination', EntityType::class, [  // Remplacez 'to_account' par 'compteDestination'
                'class' => CompteBancaire::class,
                'choice_label' => 'numeroDeCompte',  // Affiche le numéro de compte
                'label' => 'Compte destinataire',
                'placeholder' => 'Sélectionnez un compte',  // Pas de filtrage sur le destinataire
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant du virement',
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
