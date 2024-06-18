<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229232616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    //    $this->addSql('ALTER TABLE employee ADD department_id INT NOT NULL, ADD personne_id INT NOT NULL, ADD mobile VARCHAR(15) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD birthday DATE NOT NULL');
    //    $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
    //    $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id)');
    //    $this->addSql('CREATE INDEX IDX_5D9F75A1AE80F5DF ON employee (department_id)');
    //   $this->addSql('CREATE INDEX IDX_5D9F75A1A21BD112 ON employee (personne_id)');
    //    $this->addSql('ALTER TABLE `leave` ADD CONSTRAINT FK_9BB080D08C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    //    $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1AE80F5DF');
    //    $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1A21BD112');
    //    $this->addSql('DROP INDEX IDX_5D9F75A1AE80F5DF ON employee');
    //    $this->addSql('DROP INDEX IDX_5D9F75A1A21BD112 ON employee');
    //    $this->addSql('ALTER TABLE employee DROP department_id, DROP personne_id, DROP mobile, DROP address, DROP birthday');
    //    $this->addSql('ALTER TABLE `leave` DROP FOREIGN KEY FK_9BB080D08C03F15C');
    }
}
