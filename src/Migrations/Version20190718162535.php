<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190718162535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE clip_files');
        $this->addSql('DROP TABLE clips');
        $this->addSql('DROP TABLE restreamer');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('ALTER TABLE user ADD full_name VARCHAR(255) NOT NULL, ADD roles JSON NOT NULL, DROP salt, DROP is_active, CHANGE username username VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE files CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE clip_files (id INT AUTO_INCREMENT NOT NULL, clip_id INT NOT NULL, file_id INT NOT NULL, file_ord INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE clips (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE restreamer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, command LONGTEXT NOT NULL COLLATE utf8_unicode_ci, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, file_id INT NOT NULL, ord INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE files CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE user ADD salt VARCHAR(32) NOT NULL COLLATE utf8_unicode_ci, ADD is_active TINYINT(1) NOT NULL, DROP full_name, DROP roles, CHANGE username username VARCHAR(25) NOT NULL COLLATE utf8_unicode_ci, CHANGE password password VARCHAR(40) NOT NULL COLLATE utf8_unicode_ci');
    }
}
