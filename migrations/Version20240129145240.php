<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129145240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `leave` ADD employee_id INT NOT NULL, ADD leavetype_id INT NOT NULL');
        $this->addSql('ALTER TABLE `leave` ADD CONSTRAINT FK_9BB080D08C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE `leave` ADD CONSTRAINT FK_9BB080D0AB9F5BF FOREIGN KEY (leavetype_id) REFERENCES leave_type (id)');
        $this->addSql('CREATE INDEX IDX_9BB080D08C03F15C ON `leave` (employee_id)');
        $this->addSql('CREATE INDEX IDX_9BB080D0AB9F5BF ON `leave` (leavetype_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `leave` DROP FOREIGN KEY FK_9BB080D08C03F15C');
        $this->addSql('ALTER TABLE `leave` DROP FOREIGN KEY FK_9BB080D0AB9F5BF');
        $this->addSql('DROP INDEX IDX_9BB080D08C03F15C ON `leave`');
        $this->addSql('DROP INDEX IDX_9BB080D0AB9F5BF ON `leave`');
        $this->addSql('ALTER TABLE `leave` DROP employee_id, DROP leavetype_id');
    }
}
