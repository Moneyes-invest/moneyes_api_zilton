<?php

declare(strict_types = 1);

/*
 * This file is part of the Moneyes API project.
 * (c) Moneyes
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218212854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device DROP CONSTRAINT fk_92fb68ea76ed395');
        $this->addSql('DROP INDEX idx_92fb68ea76ed395');
        $this->addSql('ALTER TABLE device RENAME COLUMN user_id TO user_device_id');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E94444A30 FOREIGN KEY (user_device_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FB68E94444A30 ON device (user_device_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE device DROP CONSTRAINT FK_92FB68E94444A30');
        $this->addSql('DROP INDEX IDX_92FB68E94444A30');
        $this->addSql('ALTER TABLE device RENAME COLUMN user_device_id TO user_id');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT fk_92fb68ea76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_92fb68ea76ed395 ON device (user_id)');
    }
}
