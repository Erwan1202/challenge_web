<?php
// tests/Form/CompteBancaireFormTypeTest.php
namespace App\Tests\Form;

use App\Entity\CompteBancaire;
use App\Form\CompteBancaireFormType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Test\TypeTestCase;

class CompteBancaireFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        // Données valides pour le formulaire
        $formData = [
            'type' => 'epargne',
            'solde' => 1000,
        ];

        // Créer une instance de l'entité liée au formulaire
        $compteBancaire = new CompteBancaire();  // Supposons que CompteBancaire est une entité représentant un compte bancaire
        $form = $this->factory->create(CompteBancaireFormType::class, $compteBancaire);

        // Soumettre les données valides au formulaire
        $form->submit($formData);

        // Vérifier que le formulaire est soumis et valide
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        // Vérifier que les données du formulaire ont été assignées à l'entité
        $this->assertEquals($formData['type'], $compteBancaire->getType());
        $this->assertEquals($formData['solde'], $compteBancaire->getSolde());
    }

    public function testSubmitInvalidData()
    {
        // Données invalides (par exemple, solde négatif)
        $formData = [
            'type' => 'epargne',
            'solde' => -100,  // Solde invalide
        ];

        // Créer une instance de l'entité liée au formulaire
        $compteBancaire = new CompteBancaire();
        $form = $this->factory->create(CompteBancaireFormType::class, $compteBancaire);

        // Soumettre les données invalides au formulaire
        $form->submit($formData);

        // Vérifier que le formulaire est soumis mais invalide
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
    }

    public function testSubmitEmptyData()
    {
        // Données vides
        $formData = [
            'type' => '',
            'solde' => '',
        ];

        // Créer une instance de l'entité liée au formulaire
        $compteBancaire = new CompteBancaire();
        $form = $this->factory->create(CompteBancaireFormType::class, $compteBancaire);

        // Soumettre les données vides au formulaire
        $form->submit($formData);

        // Vérifier que le formulaire est soumis mais invalide
        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
    }
}
