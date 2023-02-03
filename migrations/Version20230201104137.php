<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230201104137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT fk_5bbfd81668afd1a0');
        $this->addSql('DROP INDEX idx_5bbfd81668afd1a0');
        $this->addSql('ALTER TABLE holding RENAME COLUMN exchange_id TO account_id');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8169B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5BBFD8169B6B5FBA ON holding (account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8169B6B5FBA');
        $this->addSql('DROP INDEX IDX_5BBFD8169B6B5FBA');
        $this->addSql('ALTER TABLE holding RENAME COLUMN account_id TO exchange_id');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT fk_5bbfd81668afd1a0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5bbfd81668afd1a0 ON holding (exchange_id)');
    }
}
