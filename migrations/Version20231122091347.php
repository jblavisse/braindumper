<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122091347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE memory_category DROP FOREIGN KEY FK_1013917ACCC80CB3');
        $this->addSql('ALTER TABLE memory_category DROP FOREIGN KEY FK_1013917A12469DE2');
        $this->addSql('DROP TABLE memory_category');
        $this->addSql('ALTER TABLE memory ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE memory ADD CONSTRAINT FK_EA6D343512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_EA6D343512469DE2 ON memory (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE memory_category (memory_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_1013917A12469DE2 (category_id), INDEX IDX_1013917ACCC80CB3 (memory_id), PRIMARY KEY(memory_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE memory_category ADD CONSTRAINT FK_1013917ACCC80CB3 FOREIGN KEY (memory_id) REFERENCES memory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memory_category ADD CONSTRAINT FK_1013917A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memory DROP FOREIGN KEY FK_EA6D343512469DE2');
        $this->addSql('DROP INDEX IDX_EA6D343512469DE2 ON memory');
        $this->addSql('ALTER TABLE memory DROP category_id');
    }
}
