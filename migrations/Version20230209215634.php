<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209215634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE transfert_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE transfert (id INT NOT NULL, asset_id VARCHAR(255) NOT NULL, asset_fees_id VARCHAR(255) DEFAULT NULL, date VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION NOT NULL, fees DOUBLE PRECISION DEFAULT NULL, external_transfert_id VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E4EACBB5DA1941 ON transfert (asset_id)');
        $this->addSql('CREATE INDEX IDX_1E4EACBBFF859273 ON transfert (asset_fees_id)');
        $this->addSql('ALTER TABLE transfert ADD CONSTRAINT FK_1E4EACBB5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transfert ADD CONSTRAINT FK_1E4EACBBFF859273 FOREIGN KEY (asset_fees_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE transfert_id_seq CASCADE');
        $this->addSql('ALTER TABLE transfert DROP CONSTRAINT FK_1E4EACBB5DA1941');
        $this->addSql('ALTER TABLE transfert DROP CONSTRAINT FK_1E4EACBBFF859273');
        $this->addSql('DROP TABLE transfert');
        $this->addSql('ALTER TABLE holding DROP date');
    }
}
