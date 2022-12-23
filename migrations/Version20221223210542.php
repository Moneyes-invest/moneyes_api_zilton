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
final class Version20221223210542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP CONSTRAINT fk_7d3656a49d86650f');
        $this->addSql('DROP INDEX idx_7d3656a49d86650f');
        $this->addSql('DROP INDEX uniq_7d3656a479f37ae5');
        $this->addSql('ALTER TABLE account DROP user_id_id');
        $this->addSql('ALTER TABLE account ALTER id_user_id DROP NOT NULL');
        $this->addSql('CREATE INDEX IDX_7D3656A479F37AE5 ON account (id_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_7D3656A479F37AE5');
        $this->addSql('ALTER TABLE account ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ALTER id_user_id SET NOT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT fk_7d3656a49d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7d3656a49d86650f ON account (user_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_7d3656a479f37ae5 ON account (id_user_id)');
    }
}
