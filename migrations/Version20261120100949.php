<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120100949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, source_id VARCHAR(255) DEFAULT NULL, blood_status VARCHAR(50) DEFAULT NULL, born VARCHAR(255) DEFAULT NULL, died VARCHAR(255) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, height VARCHAR(50) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, nationality VARCHAR(100) DEFAULT NULL, weight VARCHAR(50) DEFAULT NULL, wiki_link VARCHAR(255) DEFAULT NULL, species_id INTEGER NOT NULL, house_id INTEGER DEFAULT NULL, CONSTRAINT FK_937AB034B2A1D860 FOREIGN KEY (species_id) REFERENCES species (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_937AB0346BB74515 FOREIGN KEY (house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_937AB034B2A1D860 ON character (species_id)');
        $this->addSql('CREATE INDEX IDX_937AB0346BB74515 ON character (house_id)');
        $this->addSql('CREATE TABLE character_title (character_id INTEGER NOT NULL, title_id INTEGER NOT NULL, PRIMARY KEY (character_id, title_id), CONSTRAINT FK_D5F59F091136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D5F59F09A9F87BD FOREIGN KEY (title_id) REFERENCES title (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D5F59F091136BE75 ON character_title (character_id)');
        $this->addSql('CREATE INDEX IDX_D5F59F09A9F87BD ON character_title (title_id)');
        $this->addSql('CREATE TABLE house (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE TABLE species (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE title (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE character');
        $this->addSql('DROP TABLE character_title');
        $this->addSql('DROP TABLE house');
        $this->addSql('DROP TABLE species');
        $this->addSql('DROP TABLE title');
    }
}
