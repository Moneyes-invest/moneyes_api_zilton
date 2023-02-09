<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230209203126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT fk_2af5a5c5da1941');
        $this->addSql('ALTER TABLE asset DROP CONSTRAINT fk_2af5a5cc0f75674');
        $this->addSql('DROP INDEX idx_2af5a5cc0f75674');
        $this->addSql('DROP INDEX idx_2af5a5c5da1941');
        $this->addSql('ALTER TABLE asset DROP asset_id');
        $this->addSql('ALTER TABLE asset DROP symbol_id');
        $this->addSql('ALTER TABLE asset DROP name');
        $this->addSql('ALTER TABLE asset ALTER id TYPE VARCHAR(255)');
        $this->addSql('CREATE TABLE asset_exchange (asset_id VARCHAR(255) NOT NULL, exchange_id UUID NOT NULL, PRIMARY KEY(asset_id, exchange_id))');
        $this->addSql('CREATE INDEX IDX_5150AAF95DA1941 ON asset_exchange (asset_id)');
        $this->addSql('CREATE INDEX IDX_5150AAF968AFD1A0 ON asset_exchange (exchange_id)');
        $this->addSql('COMMENT ON COLUMN asset_exchange.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE symbol (id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE symbol_exchange (symbol_id VARCHAR(255) NOT NULL, exchange_id UUID NOT NULL, PRIMARY KEY(symbol_id, exchange_id))');
        $this->addSql('CREATE INDEX IDX_1A78D037C0F75674 ON symbol_exchange (symbol_id)');
        $this->addSql('CREATE INDEX IDX_1A78D03768AFD1A0 ON symbol_exchange (exchange_id)');
        $this->addSql('COMMENT ON COLUMN symbol_exchange.exchange_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE asset_exchange ADD CONSTRAINT FK_5150AAF95DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset_exchange ADD CONSTRAINT FK_5150AAF968AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE symbol_exchange ADD CONSTRAINT FK_1A78D037C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE symbol_exchange ADD CONSTRAINT FK_1A78D03768AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN asset.id IS NULL');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT fk_5bbfd816a76ed395');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT fk_5bbfd81638248176');
        $this->addSql('DROP INDEX idx_5bbfd81638248176');
        $this->addSql('DROP INDEX idx_5bbfd816a76ed395');
        $this->addSql('ALTER TABLE holding ADD asset_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE holding DROP user_id');
        $this->addSql('ALTER TABLE holding DROP currency_id');
        $this->addSql('ALTER TABLE holding DROP average_purchase_price');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8165DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5BBFD8165DA1941 ON holding (asset_id)');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d1a76ed395');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d138248176');
        $this->addSql('DROP INDEX idx_723705d138248176');
        $this->addSql('DROP INDEX idx_723705d1a76ed395');
        $this->addSql('ALTER TABLE transaction ADD symbol_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD asset_fees_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction DROP user_id');
        $this->addSql('ALTER TABLE transaction DROP currency_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1FF859273 FOREIGN KEY (asset_fees_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_723705D1C0F75674 ON transaction (symbol_id)');
        $this->addSql('CREATE INDEX IDX_723705D1FF859273 ON transaction (asset_fees_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1C0F75674');
        $this->addSql('ALTER TABLE asset_exchange DROP CONSTRAINT FK_5150AAF95DA1941');
        $this->addSql('ALTER TABLE asset_exchange DROP CONSTRAINT FK_5150AAF968AFD1A0');
        $this->addSql('ALTER TABLE symbol_exchange DROP CONSTRAINT FK_1A78D037C0F75674');
        $this->addSql('ALTER TABLE symbol_exchange DROP CONSTRAINT FK_1A78D03768AFD1A0');
        $this->addSql('DROP TABLE asset_exchange');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('DROP TABLE symbol_exchange');
        $this->addSql('ALTER TABLE asset ADD asset_id UUID NOT NULL');
        $this->addSql('ALTER TABLE asset ADD symbol_id UUID NOT NULL');
        $this->addSql('ALTER TABLE asset ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE asset ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN asset.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.symbol_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT fk_2af5a5c5da1941 FOREIGN KEY (asset_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT fk_2af5a5cc0f75674 FOREIGN KEY (symbol_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2af5a5cc0f75674 ON asset (symbol_id)');
        $this->addSql('CREATE INDEX idx_2af5a5c5da1941 ON asset (asset_id)');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8165DA1941');
        $this->addSql('DROP INDEX IDX_5BBFD8165DA1941');
        $this->addSql('ALTER TABLE holding ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE holding ADD currency_id UUID NOT NULL');
        $this->addSql('ALTER TABLE holding ADD average_purchase_price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE holding DROP asset_id');
        $this->addSql('COMMENT ON COLUMN holding.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT fk_5bbfd816a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT fk_5bbfd81638248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5bbfd81638248176 ON holding (currency_id)');
        $this->addSql('CREATE INDEX idx_5bbfd816a76ed395 ON holding (user_id)');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1FF859273');
        $this->addSql('DROP INDEX IDX_723705D1C0F75674');
        $this->addSql('DROP INDEX IDX_723705D1FF859273');
        $this->addSql('ALTER TABLE transaction ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD currency_id UUID NOT NULL');
        $this->addSql('ALTER TABLE transaction DROP symbol_id');
        $this->addSql('ALTER TABLE transaction DROP asset_fees_id');
        $this->addSql('COMMENT ON COLUMN transaction.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN transaction.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d1a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d138248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_723705d138248176 ON transaction (currency_id)');
        $this->addSql('CREATE INDEX idx_723705d1a76ed395 ON transaction (user_id)');
    }
}
