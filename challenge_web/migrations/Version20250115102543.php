<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250115102543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP mdp_chiffre');
        $this->addSql('ALTER TABLE utilisateur DROP role');
        $this->addSql('ALTER TABLE utilisateur ALTER email TYPE VARCHAR(180)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON utilisateur (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('ALTER TABLE utilisateur ADD mdp_chiffre TEXT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD role VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP roles');
        $this->addSql('ALTER TABLE utilisateur DROP password');
        $this->addSql('ALTER TABLE utilisateur ALTER email TYPE VARCHAR(255)');
    }
}
