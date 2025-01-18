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
        // Vérification que l'option 'user' est passée
        if (!isset($options['user'])) {
            throw new \InvalidArgumentException('The "user" option is mandatory');
        }

        $builder
            ->add('compteSource', EntityType::class, [
                'class' => CompteBancaire::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    // Limite les comptes affichés à ceux de l'utilisateur connecté
                    return $er->createQueryBuilder('cb')
                        ->where('cb.utilisateur = :user')
                        ->setParameter('user', $options['user']);
                },
                'choice_label' => 'numeroDeCompte', // Affiche le numéro de compte dans le formulaire
                'label' => 'Compte à créditer',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR', // Monnaie pour le champ
                'label' => 'Montant à déposer',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Effectuer le dépôt',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class, // Lier le formulaire à l'entité Transaction
            'user' => null, // Option personnalisée pour passer l'utilisateur
        ]);
    }
}
