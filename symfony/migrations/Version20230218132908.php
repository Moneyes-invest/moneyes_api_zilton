<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218132908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE device_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE device (id INT NOT NULL, user_id UUID DEFAULT NULL, os_version VARCHAR(255) DEFAULT NULL, lang VARCHAR(255) DEFAULT NULL, os_name VARCHAR(255) DEFAULT NULL, device_uuid VARCHAR(255) DEFAULT NULL, device_type VARCHAR(255) DEFAULT NULL, device_name VARCHAR(255) DEFAULT NULL, device_model VARCHAR(255) DEFAULT NULL, app_version VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92FB68EA76ED395 ON device (user_id)');
        $this->addSql('COMMENT ON COLUMN device.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68EA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE device_id_seq CASCADE');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68EA76ED395');
        $this->addSql('DROP TABLE device');
    }
}
