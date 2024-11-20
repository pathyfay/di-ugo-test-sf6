<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119142748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, firstname VARCHAR(50) DEFAULT NULL, lastname VARCHAR(50) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, mobile VARCHAR(30) DEFAULT NULL, birthday DATETIME DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat_civil (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, product_id INT NOT NULL, order_date DATETIME NOT NULL, quantity INT NOT NULL, price INT NOT NULL, currency VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_F52993989395C3F3 (customer_id), INDEX IDX_F52993984584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisme (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, reference VARCHAR(100) NOT NULL, note VARCHAR(255) NOT NULL, logo LONGBLOB NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, birthday DATETIME NOT NULL, image VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, sexe VARCHAR(1) NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, reference VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, organisme_id INT DEFAULT NULL, type VARCHAR(100) DEFAULT NULL, montant_total DOUBLE PRECISION NOT NULL, montant_restant DOUBLE PRECISION NOT NULL, mensualite DOUBLE PRECISION DEFAULT NULL, taux NUMERIC(10, 0) NOT NULL, reserve DOUBLE PRECISION NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, date_prelevement DATETIME NOT NULL, operation VARCHAR(255) NOT NULL, utilisateur VARCHAR(255) NOT NULL, INDEX IDX_939F45445DDD38F5 (organisme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_ressource (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F45445DDD38F5 FOREIGN KEY (organisme_id) REFERENCES organisme (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F45445DDD38F5');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE etat_civil');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE organisme');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE type_ressource');
        $this->addSql('DROP TABLE user');
    }
}
