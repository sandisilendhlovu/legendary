<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110075158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flow (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL COMMENT \'The product this flow belongs to\', title VARCHAR(150) NOT NULL COMMENT \'Short title for this troubleshooting flow\', description LONGTEXT DEFAULT NULL COMMENT \'Optional description or summary of the troubleshooting flow\', INDEX IDX_52C0D6704584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flow ADD CONSTRAINT FK_52C0D6704584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flow DROP FOREIGN KEY FK_52C0D6704584665A');
        $this->addSql('DROP TABLE flow');
    }
}
