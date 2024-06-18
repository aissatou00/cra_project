<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229220320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `leave` ADD CONSTRAINT FK_9BB080D08C03F15C FOREIGN KEY (employee_id) REFERENCES personne (id)');
        $this->addSql('ALTER TABLE personne ADD department_id INT NOT NULL, ADD personne_id INT NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD mobile VARCHAR(15) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD birthday DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE personne ADD CONSTRAINT FK_FCEC9EFA21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id)');
        $this->addSql('CREATE INDEX IDX_FCEC9EFAE80F5DF ON personne (department_id)');
        $this->addSql('CREATE INDEX IDX_FCEC9EFA21BD112 ON personne (personne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `leave` DROP FOREIGN KEY FK_9BB080D08C03F15C');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFAE80F5DF');
        $this->addSql('ALTER TABLE personne DROP FOREIGN KEY FK_FCEC9EFA21BD112');
        $this->addSql('DROP INDEX IDX_FCEC9EFAE80F5DF ON personne');
        $this->addSql('DROP INDEX IDX_FCEC9EFA21BD112 ON personne');
        $this->addSql('ALTER TABLE personne DROP department_id, DROP personne_id, DROP type, DROP mobile, DROP address, DROP birthday');
    }
}
