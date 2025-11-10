<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110164505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flow_step (id INT AUTO_INCREMENT NOT NULL, flow_id INT NOT NULL COMMENT \'The flow this step belongs to\', step_number INT NOT NULL COMMENT \'Order number of this step within its flow\', title VARCHAR(150) NOT NULL COMMENT \'Short title describing this troubleshooting step\', content LONGTEXT NOT NULL COMMENT \'Detailed instructions or information for this troubleshooting step\', INDEX IDX_FD53213B7EB60D1B (flow_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flow_step ADD CONSTRAINT FK_FD53213B7EB60D1B FOREIGN KEY (flow_id) REFERENCES flow (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flow_step DROP FOREIGN KEY FK_FD53213B7EB60D1B');
        $this->addSql('DROP TABLE flow_step');
    }
}
