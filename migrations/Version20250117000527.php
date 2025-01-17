<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117000527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_bancaire DROP CONSTRAINT compte_bancaire_utilisateur_id_fkey');
        $this->addSql('ALTER TABLE compte_bancaire ADD CONSTRAINT FK_50BC21DEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT transaction_compte_source_id_fkey');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT transaction_compte_destination_id_fkey');
        $this->addSql('ALTER TABLE transaction ALTER date_heure TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE transaction ALTER date_heure DROP DEFAULT');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D156B22253 FOREIGN KEY (compte_source_id) REFERENCES compte_bancaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D14105B733 FOREIGN KEY (compte_destination_id) REFERENCES compte_bancaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE utilisateur ALTER roles TYPE JSON');
        $this->addSql('CREATE UNIQUE INDEX unique_email ON utilisateur (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE compte_bancaire DROP CONSTRAINT FK_50BC21DEFB88E14F');
        $this->addSql('ALTER TABLE compte_bancaire ADD CONSTRAINT compte_bancaire_utilisateur_id_fkey FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX unique_email');
        $this->addSql('ALTER TABLE utilisateur ALTER roles TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D156B22253');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D14105B733');
        $this->addSql('ALTER TABLE transaction ALTER date_heure TYPE TIMESTAMP(0) WITH TIME ZONE');
        $this->addSql('ALTER TABLE transaction ALTER date_heure SET DEFAULT \'now()\'');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT transaction_compte_source_id_fkey FOREIGN KEY (compte_source_id) REFERENCES compte_bancaire (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT transaction_compte_destination_id_fkey FOREIGN KEY (compte_destination_id) REFERENCES compte_bancaire (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
