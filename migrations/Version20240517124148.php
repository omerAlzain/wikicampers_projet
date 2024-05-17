<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517124148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT fk_42c849552b9d6493');
        $this->addSql('DROP INDEX idx_42c849552b9d6493');
        $this->addSql('ALTER TABLE reservation RENAME COLUMN disponibilite_id TO vehicule_id');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C849554A4A3511 ON reservation (vehicule_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849554A4A3511');
        $this->addSql('DROP INDEX IDX_42C849554A4A3511');
        $this->addSql('ALTER TABLE reservation RENAME COLUMN vehicule_id TO disponibilite_id');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT fk_42c849552b9d6493 FOREIGN KEY (disponibilite_id) REFERENCES disponibilite (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_42c849552b9d6493 ON reservation (disponibilite_id)');
    }
}
