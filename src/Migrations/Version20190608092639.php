<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190608092639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE nomenclature (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER NOT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_799A3652B03A8386 ON nomenclature (created_by_id)');
        $this->addSql('CREATE TABLE image_category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_C53D045F12469DE2 ON image (category_id)');
        $this->addSql('CREATE TABLE fos_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , facebook_id VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479C05FB297 ON fos_user (confirmation_token)');
        $this->addSql('CREATE TABLE card (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nomenclature_id INTEGER DEFAULT NULL, image_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, description_with_gaps CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_161498D390BFD4B8 ON card (nomenclature_id)');
        $this->addSql('CREATE INDEX IDX_161498D33DA5256D ON card (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE nomenclature');
        $this->addSql('DROP TABLE image_category');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE card');
    }
}
