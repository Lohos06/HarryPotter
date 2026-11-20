<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261120105228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, source_id VARCHAR(255) DEFAULT NULL, blood_status VARCHAR(50) DEFAULT NULL, born VARCHAR(255) DEFAULT NULL, died VARCHAR(255) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, height VARCHAR(50) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, nationality VARCHAR(100) DEFAULT NULL, weight VARCHAR(50) DEFAULT NULL, wiki_link VARCHAR(255) DEFAULT NULL, species_id INTEGER NOT NULL, house_id INTEGER DEFAULT NULL, CONSTRAINT FK_937AB034B2A1D860 FOREIGN KEY (species_id) REFERENCES species (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_937AB0346BB74515 FOREIGN KEY (house_id) REFERENCES house (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO character (id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id) SELECT id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
        $this->addSql('CREATE INDEX IDX_937AB0346BB74515 ON character (house_id)');
        $this->addSql('CREATE INDEX IDX_937AB034B2A1D860 ON character (species_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_937AB034953C1C61 ON character (source_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__house AS SELECT id, name FROM house');
        $this->addSql('DROP TABLE house');
        $this->addSql('CREATE TABLE house (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO house (id, name) SELECT id, name FROM __temp__house');
        $this->addSql('DROP TABLE __temp__house');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_67D5399D5E237E06 ON house (name)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__species AS SELECT id, name FROM species');
        $this->addSql('DROP TABLE species');
        $this->addSql('CREATE TABLE species (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO species (id, name) SELECT id, name FROM __temp__species');
        $this->addSql('DROP TABLE __temp__species');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A50FF7125E237E06 ON species (name)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__title AS SELECT id, name FROM title');
        $this->addSql('DROP TABLE title');
        $this->addSql('CREATE TABLE title (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO title (id, name) SELECT id, name FROM __temp__title');
        $this->addSql('DROP TABLE __temp__title');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B36786B5E237E06 ON title (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, source_id VARCHAR(255) DEFAULT NULL, blood_status VARCHAR(50) DEFAULT NULL, born VARCHAR(255) DEFAULT NULL, died VARCHAR(255) DEFAULT NULL, gender VARCHAR(20) DEFAULT NULL, height VARCHAR(50) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, nationality VARCHAR(100) DEFAULT NULL, weight VARCHAR(50) DEFAULT NULL, wiki_link VARCHAR(255) DEFAULT NULL, species_id INTEGER NOT NULL, house_id INTEGER DEFAULT NULL, CONSTRAINT FK_937AB034B2A1D860 FOREIGN KEY (species_id) REFERENCES species (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_937AB0346BB74515 FOREIGN KEY (house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO character (id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id) SELECT id, source_id, blood_status, born, died, gender, height, image, name, nationality, weight, wiki_link, species_id, house_id FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
        $this->addSql('CREATE INDEX IDX_937AB034B2A1D860 ON character (species_id)');
        $this->addSql('CREATE INDEX IDX_937AB0346BB74515 ON character (house_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__house AS SELECT id, name FROM house');
        $this->addSql('DROP TABLE house');
        $this->addSql('CREATE TABLE house (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO house (id, name) SELECT id, name FROM __temp__house');
        $this->addSql('DROP TABLE __temp__house');
        $this->addSql('CREATE TEMPORARY TABLE __temp__species AS SELECT id, name FROM species');
        $this->addSql('DROP TABLE species');
        $this->addSql('CREATE TABLE species (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO species (id, name) SELECT id, name FROM __temp__species');
        $this->addSql('DROP TABLE __temp__species');
        $this->addSql('CREATE TEMPORARY TABLE __temp__title AS SELECT id, name FROM title');
        $this->addSql('DROP TABLE title');
        $this->addSql('CREATE TABLE title (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO title (id, name) SELECT id, name FROM __temp__title');
        $this->addSql('DROP TABLE __temp__title');
    }
}
