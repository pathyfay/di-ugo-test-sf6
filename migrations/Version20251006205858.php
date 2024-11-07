<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251006205858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, title VARCHAR(100) NOT NULL, message LONGTEXT DEFAULT NULL, type VARCHAR(50) NOT NULL, channels LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', scheduled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sent TINYINT(1) NOT NULL, INDEX IDX_BF5476CAB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_target (id INT AUTO_INCREMENT NOT NULL, notification_id INT NOT NULL, user_id INT NOT NULL, channel VARCHAR(20) NOT NULL, delivered TINYINT(1) NOT NULL, delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_82C8D1A1EF1A9D84 (notification_id), INDEX IDX_82C8D1A1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relation_child (id INT AUTO_INCREMENT NOT NULL, relation_id INT NOT NULL, child_id INT NOT NULL, INDEX IDX_B10D81B73256915B (relation_id), INDEX IDX_B10D81B7DD62C21B (child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE relation_child_parents (relation_child_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_EA73F095FA79F8A3 (relation_child_id), INDEX IDX_EA73F095A76ED395 (user_id), PRIMARY KEY(relation_child_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_relation (id INT AUTO_INCREMENT NOT NULL, user1_id INT NOT NULL, user2_id INT DEFAULT NULL, user3_id INT DEFAULT NULL, user4_id INT DEFAULT NULL, civil_status_id INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', wedding_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', divorce_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8204A34956AE248B (user1_id), INDEX IDX_8204A349441B8B65 (user2_id), INDEX IDX_8204A349FCA7EC00 (user3_id), INDEX IDX_8204A3496170D4B9 (user4_id), INDEX IDX_8204A34996DE64FD (civil_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_relation_others (user_relation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2541DBEF9B4D58CE (user_relation_id), INDEX IDX_2541DBEFA76ED395 (user_id), PRIMARY KEY(user_relation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification_target ADD CONSTRAINT FK_82C8D1A1EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notification_target ADD CONSTRAINT FK_82C8D1A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE relation_child ADD CONSTRAINT FK_B10D81B73256915B FOREIGN KEY (relation_id) REFERENCES user_relation (id)');
        $this->addSql('ALTER TABLE relation_child ADD CONSTRAINT FK_B10D81B7DD62C21B FOREIGN KEY (child_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE relation_child_parents ADD CONSTRAINT FK_EA73F095FA79F8A3 FOREIGN KEY (relation_child_id) REFERENCES relation_child (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE relation_child_parents ADD CONSTRAINT FK_EA73F095A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A34956AE248B FOREIGN KEY (user1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A349441B8B65 FOREIGN KEY (user2_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A349FCA7EC00 FOREIGN KEY (user3_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A3496170D4B9 FOREIGN KEY (user4_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_relation ADD CONSTRAINT FK_8204A34996DE64FD FOREIGN KEY (civil_status_id) REFERENCES civil_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_relation_others ADD CONSTRAINT FK_2541DBEF9B4D58CE FOREIGN KEY (user_relation_id) REFERENCES user_relation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_relation_others ADD CONSTRAINT FK_2541DBEFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_product CHANGE name nom VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE civility ADD description VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB03A8386');
        $this->addSql('ALTER TABLE notification_target DROP FOREIGN KEY FK_82C8D1A1EF1A9D84');
        $this->addSql('ALTER TABLE notification_target DROP FOREIGN KEY FK_82C8D1A1A76ED395');
        $this->addSql('ALTER TABLE relation_child DROP FOREIGN KEY FK_B10D81B73256915B');
        $this->addSql('ALTER TABLE relation_child DROP FOREIGN KEY FK_B10D81B7DD62C21B');
        $this->addSql('ALTER TABLE relation_child_parents DROP FOREIGN KEY FK_EA73F095FA79F8A3');
        $this->addSql('ALTER TABLE relation_child_parents DROP FOREIGN KEY FK_EA73F095A76ED395');
        $this->addSql('ALTER TABLE user_relation DROP FOREIGN KEY FK_8204A34956AE248B');
        $this->addSql('ALTER TABLE user_relation DROP FOREIGN KEY FK_8204A349441B8B65');
        $this->addSql('ALTER TABLE user_relation DROP FOREIGN KEY FK_8204A349FCA7EC00');
        $this->addSql('ALTER TABLE user_relation DROP FOREIGN KEY FK_8204A3496170D4B9');
        $this->addSql('ALTER TABLE user_relation DROP FOREIGN KEY FK_8204A34996DE64FD');
        $this->addSql('ALTER TABLE user_relation_others DROP FOREIGN KEY FK_2541DBEF9B4D58CE');
        $this->addSql('ALTER TABLE user_relation_others DROP FOREIGN KEY FK_2541DBEFA76ED395');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_target');
        $this->addSql('DROP TABLE relation_child');
        $this->addSql('DROP TABLE relation_child_parents');
        $this->addSql('DROP TABLE user_relation');
        $this->addSql('DROP TABLE user_relation_others');
        $this->addSql('ALTER TABLE category_product CHANGE nom name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE civility DROP description');
    }
}
