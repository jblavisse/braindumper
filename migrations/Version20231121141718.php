<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121141718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE memory_category (memory_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_1013917ACCC80CB3 (memory_id), INDEX IDX_1013917A12469DE2 (category_id), PRIMARY KEY(memory_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE memory_category ADD CONSTRAINT FK_1013917ACCC80CB3 FOREIGN KEY (memory_id) REFERENCES memory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memory_category ADD CONSTRAINT FK_1013917A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memory ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE memory ADD CONSTRAINT FK_EA6D3435C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_EA6D3435C54C8C93 ON memory (type_id)');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE memory DROP FOREIGN KEY FK_EA6D3435C54C8C93');
        $this->addSql('ALTER TABLE memory_category DROP FOREIGN KEY FK_1013917ACCC80CB3');
        $this->addSql('ALTER TABLE memory_category DROP FOREIGN KEY FK_1013917A12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE memory_category');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP INDEX IDX_EA6D3435C54C8C93 ON memory');
        $this->addSql('ALTER TABLE memory DROP type_id');
        $this->addSql('ALTER TABLE user DROP reset_token');
    }
}