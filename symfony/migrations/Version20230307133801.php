<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307133801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holding (id INT NOT NULL, account_id UUID NOT NULL, asset_id UUID NOT NULL, quantity DOUBLE PRECISION NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, average_purchase_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BBFD8169B6B5FBA ON holding (account_id)');
        $this->addSql('CREATE INDEX IDX_5BBFD8165DA1941 ON holding (asset_id)');
        $this->addSql('COMMENT ON COLUMN holding.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN holding.asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8169B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding ADD CONSTRAINT FK_5BBFD8165DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_user_user_permissions DROP CONSTRAINT auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id');
        $this->addSql('ALTER TABLE auth_user_user_permissions DROP CONSTRAINT auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm');
        $this->addSql('ALTER TABLE auth_user_groups DROP CONSTRAINT auth_user_groups_user_id_6a12ed8b_fk_auth_user_id');
        $this->addSql('ALTER TABLE auth_user_groups DROP CONSTRAINT auth_user_groups_group_id_97559544_fk_auth_group_id');
        $this->addSql('ALTER TABLE auth_group_permissions DROP CONSTRAINT auth_group_permissions_group_id_b120cbf9_fk_auth_group_id');
        $this->addSql('ALTER TABLE auth_group_permissions DROP CONSTRAINT auth_group_permissio_permission_id_84c5c92e_fk_auth_perm');
        $this->addSql('ALTER TABLE django_admin_log DROP CONSTRAINT django_admin_log_content_type_id_c4bce8eb_fk_django_co');
        $this->addSql('ALTER TABLE django_admin_log DROP CONSTRAINT django_admin_log_user_id_c564eba6_fk_auth_user_id');
        $this->addSql('ALTER TABLE auth_permission DROP CONSTRAINT auth_permission_content_type_id_2f476e4b_fk_django_co');
        $this->addSql('DROP TABLE auth_user_user_permissions');
        $this->addSql('DROP TABLE django_migrations');
        $this->addSql('DROP TABLE django_session');
        $this->addSql('DROP TABLE auth_user_groups');
        $this->addSql('DROP TABLE auth_user');
        $this->addSql('DROP TABLE auth_group_permissions');
        $this->addSql('DROP TABLE auth_group');
        $this->addSql('DROP TABLE django_admin_log');
        $this->addSql('DROP TABLE auth_permission');
        $this->addSql('DROP TABLE django_content_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE auth_user_user_permissions (id BIGINT NOT NULL, user_id INT NOT NULL, permission_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_user_user_permissions_permission_id_1fbb5f2c ON auth_user_user_permissions (permission_id)');
        $this->addSql('CREATE INDEX auth_user_user_permissions_user_id_a95ead1b ON auth_user_user_permissions (user_id)');
        $this->addSql('CREATE UNIQUE INDEX auth_user_user_permissions_user_id_permission_id_14a6b632_uniq ON auth_user_user_permissions (user_id, permission_id)');
        $this->addSql('CREATE TABLE django_migrations (id BIGINT NOT NULL, app VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, applied TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE django_session (session_key VARCHAR(40) NOT NULL, session_data TEXT NOT NULL, expire_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(session_key))');
        $this->addSql('CREATE INDEX django_session_expire_date_a5c62663 ON django_session (expire_date)');
        $this->addSql('CREATE INDEX django_session_session_key_c0390e0f_like ON django_session (session_key)');
        $this->addSql('CREATE TABLE auth_user_groups (id BIGINT NOT NULL, user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_user_groups_group_id_97559544 ON auth_user_groups (group_id)');
        $this->addSql('CREATE INDEX auth_user_groups_user_id_6a12ed8b ON auth_user_groups (user_id)');
        $this->addSql('CREATE UNIQUE INDEX auth_user_groups_user_id_group_id_94350c0c_uniq ON auth_user_groups (user_id, group_id)');
        $this->addSql('CREATE TABLE auth_user (id INT NOT NULL, password VARCHAR(128) NOT NULL, last_login TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, is_superuser BOOLEAN NOT NULL, username VARCHAR(150) NOT NULL, first_name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, email VARCHAR(254) NOT NULL, is_staff BOOLEAN NOT NULL, is_active BOOLEAN NOT NULL, date_joined TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_user_username_6821ab7c_like ON auth_user (username)');
        $this->addSql('CREATE UNIQUE INDEX auth_user_username_key ON auth_user (username)');
        $this->addSql('CREATE TABLE auth_group_permissions (id BIGINT NOT NULL, group_id INT NOT NULL, permission_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_group_permissions_permission_id_84c5c92e ON auth_group_permissions (permission_id)');
        $this->addSql('CREATE INDEX auth_group_permissions_group_id_b120cbf9 ON auth_group_permissions (group_id)');
        $this->addSql('CREATE UNIQUE INDEX auth_group_permissions_group_id_permission_id_0cd325b0_uniq ON auth_group_permissions (group_id, permission_id)');
        $this->addSql('CREATE TABLE auth_group (id INT NOT NULL, name VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_group_name_a6ea08ec_like ON auth_group (name)');
        $this->addSql('CREATE UNIQUE INDEX auth_group_name_key ON auth_group (name)');
        $this->addSql('CREATE TABLE django_admin_log (id INT NOT NULL, content_type_id INT DEFAULT NULL, user_id INT NOT NULL, action_time TIMESTAMP(0) WITH TIME ZONE NOT NULL, object_id TEXT DEFAULT NULL, object_repr VARCHAR(200) NOT NULL, action_flag SMALLINT NOT NULL, change_message TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX django_admin_log_user_id_c564eba6 ON django_admin_log (user_id)');
        $this->addSql('CREATE INDEX django_admin_log_content_type_id_c4bce8eb ON django_admin_log (content_type_id)');
        $this->addSql('CREATE TABLE auth_permission (id INT NOT NULL, content_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, codename VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX auth_permission_content_type_id_2f476e4b ON auth_permission (content_type_id)');
        $this->addSql('CREATE UNIQUE INDEX auth_permission_content_type_id_codename_01ab375a_uniq ON auth_permission (content_type_id, codename)');
        $this->addSql('CREATE TABLE django_content_type (id INT NOT NULL, app_label VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX django_content_type_app_label_model_76bd3d3b_uniq ON django_content_type (app_label, model)');
        $this->addSql('ALTER TABLE auth_user_user_permissions ADD CONSTRAINT auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_user_user_permissions ADD CONSTRAINT auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm FOREIGN KEY (permission_id) REFERENCES auth_permission (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_user_groups ADD CONSTRAINT auth_user_groups_user_id_6a12ed8b_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_user_groups ADD CONSTRAINT auth_user_groups_group_id_97559544_fk_auth_group_id FOREIGN KEY (group_id) REFERENCES auth_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_group_permissions ADD CONSTRAINT auth_group_permissions_group_id_b120cbf9_fk_auth_group_id FOREIGN KEY (group_id) REFERENCES auth_group (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_group_permissions ADD CONSTRAINT auth_group_permissio_permission_id_84c5c92e_fk_auth_perm FOREIGN KEY (permission_id) REFERENCES auth_permission (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE django_admin_log ADD CONSTRAINT django_admin_log_content_type_id_c4bce8eb_fk_django_co FOREIGN KEY (content_type_id) REFERENCES django_content_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE django_admin_log ADD CONSTRAINT django_admin_log_user_id_c564eba6_fk_auth_user_id FOREIGN KEY (user_id) REFERENCES auth_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_permission ADD CONSTRAINT auth_permission_content_type_id_2f476e4b_fk_django_co FOREIGN KEY (content_type_id) REFERENCES django_content_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8169B6B5FBA');
        $this->addSql('ALTER TABLE holding DROP CONSTRAINT FK_5BBFD8165DA1941');
        $this->addSql('DROP TABLE holding');
    }
}
