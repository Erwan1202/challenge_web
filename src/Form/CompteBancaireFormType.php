<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompteBancaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de compte',
                'choices' => [
                    'Épargne' => 'epargne',
                    'Courant' => 'courant',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez choisir un type de compte']),
                ],
            ])
            ->add('solde', NumberType::class, [
                'label' => 'Solde initial',
                'required' => true,
            ]);
    }
}
