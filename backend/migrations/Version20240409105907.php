<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409105907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE password_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE password_token (id INT NOT NULL, admin_id INT NOT NULL, token VARCHAR(50) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BEAB6C245F37A13B ON password_token (token)');
        $this->addSql('CREATE INDEX IDX_BEAB6C24642B8210 ON password_token (admin_id)');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24642B8210 FOREIGN KEY (admin_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE password_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT FK_BEAB6C24642B8210');
        $this->addSql('DROP TABLE password_token');
    }
}
