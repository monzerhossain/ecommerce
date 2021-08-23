<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210816235639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, api_token VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('DROP INDEX IDX_FA7AEFFB4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__attribute AS SELECT id, product_id, name, value FROM attribute');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('CREATE TABLE attribute (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, value VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_FA7AEFFB4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO attribute (id, product_id, name, value) SELECT id, product_id, name, value FROM __temp__attribute');
        $this->addSql('DROP TABLE __temp__attribute');
        $this->addSql('CREATE INDEX IDX_FA7AEFFB4584665A ON attribute (product_id)');
        $this->addSql('DROP INDEX IDX_149244D312469DE2');
        $this->addSql('DROP INDEX IDX_149244D34584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__category_product AS SELECT category_id, product_id FROM category_product');
        $this->addSql('DROP TABLE category_product');
        $this->addSql('CREATE TABLE category_product (category_id INTEGER NOT NULL, product_id INTEGER NOT NULL, PRIMARY KEY(category_id, product_id), CONSTRAINT FK_149244D312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_149244D34584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO category_product (category_id, product_id) SELECT category_id, product_id FROM __temp__category_product');
        $this->addSql('DROP TABLE __temp__category_product');
        $this->addSql('CREATE INDEX IDX_149244D312469DE2 ON category_product (category_id)');
        $this->addSql('CREATE INDEX IDX_149244D34584665A ON category_product (product_id)');
        $this->addSql('DROP INDEX IDX_C53D045F4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, product_id, url, height, weight FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, url VARCHAR(255) NOT NULL COLLATE BINARY, height DOUBLE PRECISION NOT NULL, weight DOUBLE PRECISION NOT NULL, CONSTRAINT FK_C53D045F4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO image (id, product_id, url, height, weight) SELECT id, product_id, url, height, weight FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE INDEX IDX_C53D045F4584665A ON image (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_FA7AEFFB4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__attribute AS SELECT id, product_id, name, value FROM attribute');
        $this->addSql('DROP TABLE attribute');
        $this->addSql('CREATE TABLE attribute (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO attribute (id, product_id, name, value) SELECT id, product_id, name, value FROM __temp__attribute');
        $this->addSql('DROP TABLE __temp__attribute');
        $this->addSql('CREATE INDEX IDX_FA7AEFFB4584665A ON attribute (product_id)');
        $this->addSql('DROP INDEX IDX_149244D312469DE2');
        $this->addSql('DROP INDEX IDX_149244D34584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__category_product AS SELECT category_id, product_id FROM category_product');
        $this->addSql('DROP TABLE category_product');
        $this->addSql('CREATE TABLE category_product (category_id INTEGER NOT NULL, product_id INTEGER NOT NULL, PRIMARY KEY(category_id, product_id))');
        $this->addSql('INSERT INTO category_product (category_id, product_id) SELECT category_id, product_id FROM __temp__category_product');
        $this->addSql('DROP TABLE __temp__category_product');
        $this->addSql('CREATE INDEX IDX_149244D312469DE2 ON category_product (category_id)');
        $this->addSql('CREATE INDEX IDX_149244D34584665A ON category_product (product_id)');
        $this->addSql('DROP INDEX IDX_C53D045F4584665A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, product_id, url, height, weight FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, url VARCHAR(255) NOT NULL, height DOUBLE PRECISION NOT NULL, weight DOUBLE PRECISION NOT NULL)');
        $this->addSql('INSERT INTO image (id, product_id, url, height, weight) SELECT id, product_id, url, height, weight FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE INDEX IDX_C53D045F4584665A ON image (product_id)');
    }
}
