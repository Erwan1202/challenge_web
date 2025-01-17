<?php

namespace App\Form;

use App\Entity\BankAccount;
use App\Entity\Transaction;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepositFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!isset($options['user'])) {
            throw new \InvalidArgumentException('The "user" option is mandatory');
        }

        $builder
            ->add('ToAccount', EntityType::class, [
                'label' => 'Compte',
                'class' => BankAccount::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('ba')
                        ->where('ba.Users = :user')
                        ->setParameter('user', $options['user']);
                },
                'choice_label' => 'Name',
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('Amount', NumberType::class, [
                'label' => 'Montant',
                'required' => true,
                'scale' => 2,
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('Label', TextType::class, [
                'label' => 'Libellé',
                'required' => false,
                'attr' => ['class' => 'form-control input-field'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Effectuer le dépôt',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'user' => null,
        ]);
    }
}
