<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110172049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flow_step_option (id INT AUTO_INCREMENT NOT NULL, current_step_id INT NOT NULL COMMENT \'The FlowStep this option belongs to\', next_step_id INT DEFAULT NULL COMMENT \'The next step triggered when this option is selected\', label VARCHAR(150) NOT NULL COMMENT \'Button label that represents this choice in the flow\', INDEX IDX_CE9EBB69D9BF9B19 (current_step_id), INDEX IDX_CE9EBB69B13C343E (next_step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flow_step_option ADD CONSTRAINT FK_CE9EBB69D9BF9B19 FOREIGN KEY (current_step_id) REFERENCES flow_step (id)');
        $this->addSql('ALTER TABLE flow_step_option ADD CONSTRAINT FK_CE9EBB69B13C343E FOREIGN KEY (next_step_id) REFERENCES flow_step (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE flow_step_option DROP FOREIGN KEY FK_CE9EBB69D9BF9B19');
        $this->addSql('ALTER TABLE flow_step_option DROP FOREIGN KEY FK_CE9EBB69B13C343E');
        $this->addSql('DROP TABLE flow_step_option');
    }
}
