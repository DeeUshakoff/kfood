<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231224132921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_ingredients_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_tags_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, name VARCHAR(63) NOT NULL, price INT NOT NULL, description VARCHAR(1023) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_image (id INT NOT NULL, product_id INT NOT NULL, image_path VARCHAR(1023) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_ingredients (id INT NOT NULL, product_id INT NOT NULL, ingredient_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_tags (id INT NOT NULL, product_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(31) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_image_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_ingredients_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_tags_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE product_ingredients');
        $this->addSql('DROP TABLE product_tags');
        $this->addSql('DROP TABLE tag');
    }
}
