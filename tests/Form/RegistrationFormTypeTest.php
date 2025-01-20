<?php
namespace App\Tests\Form;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class RegistrationFormTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@test.com',
            'plainPassword' => 'password123',
            'agreeTerms' => true,
            'telephone' => '0601020304',
        ];

        $model = new Utilisateur();
        $form = $this->factory->create(RegistrationFormType::class, $model);

        $expected = new Utilisateur();
        $expected->setNom('Dupont');
        $expected->setPrenom('Jean');
        $expected->setEmail('jean.dupont@test.com');
        $expected->setTelephone('0601020304');
        // Notez que le mot de passe en clair et l'acceptation des termes ne sont pas mappés directement sur l'entité

        // Soumettez les données au formulaire
        $form->submit($formData);

        // Vérifiez que le formulaire est synchronisé
        $this->assertTrue($form->isSynchronized());

        // Vérifiez que les données du formulaire correspondent aux données attendues
        $this->assertEquals($expected->getNom(), $model->getNom());
        $this->assertEquals($expected->getPrenom(), $model->getPrenom());
        $this->assertEquals($expected->getEmail(), $model->getEmail());
        $this->assertEquals($expected->getTelephone(), $model->getTelephone());

        // Vérifiez que les champs du formulaire existent
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
