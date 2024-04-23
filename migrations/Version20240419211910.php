<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419211910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE carts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE carts (id INT NOT NULL, user_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E004AACA76ED395 ON carts (user_id)');
        $this->addSql('CREATE TABLE cart_products (cart_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(cart_id, product_id))');
        $this->addSql('CREATE INDEX IDX_2D2515311AD5CDBF ON cart_products (cart_id)');
        $this->addSql('CREATE INDEX IDX_2D2515314584665A ON cart_products (product_id)');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D2515311AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D2515314584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE carts_id_seq CASCADE');
        $this->addSql('ALTER TABLE carts DROP CONSTRAINT FK_4E004AACA76ED395');
        $this->addSql('ALTER TABLE cart_products DROP CONSTRAINT FK_2D2515311AD5CDBF');
        $this->addSql('ALTER TABLE cart_products DROP CONSTRAINT FK_2D2515314584665A');
        $this->addSql('DROP TABLE carts');
        $this->addSql('DROP TABLE cart_products');
    }
}
