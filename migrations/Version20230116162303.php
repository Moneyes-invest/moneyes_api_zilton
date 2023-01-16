<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230116162303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset (id UUID NOT NULL, asset_id UUID NOT NULL, symbol_id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF5A5C5DA1941 ON asset (asset_id)');
        $this->addSql('CREATE INDEX IDX_2AF5A5CC0F75674 ON asset (symbol_id)');
        $this->addSql('COMMENT ON COLUMN asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.symbol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5C5DA1941 FOREIGN KEY (asset_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CC0F75674 FOREIGN KEY (symbol_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ALTER quantity TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE holding ALTER average_purchase_price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE transaction ALTER fees TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE transaction ALTER fees SET NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE transaction ALTER quantity TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5C5DA1941');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT FK_2AF5A5CC0F75674');
        $this->addSql('DROP TABLE asset');
        $this->addSql('ALTER TABLE transaction ALTER fees TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE transaction ALTER fees DROP NOT NULL');
        $this->addSql('ALTER TABLE transaction ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE transaction ALTER quantity TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE holding ALTER quantity TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE holding ALTER average_purchase_price TYPE NUMERIC(10, 2)');
    }
}
