<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261109132308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, title, date, information, category, places, image, validated FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, information CLOB NOT NULL, category VARCHAR(50) NOT NULL, places INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL, validated BOOLEAN NOT NULL, author_id INTEGER DEFAULT NULL, CONSTRAINT FK_3BAE0AA7F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO event (id, title, date, information, category, places, image, validated) SELECT id, title, date, information, category, places, image, validated FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7F675F31B ON event (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, title, date, information, category, places, image, validated FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATETIME NOT NULL, information CLOB NOT NULL, category VARCHAR(50) NOT NULL, places INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL, validated BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO event (id, title, date, information, category, places, image, validated) SELECT id, title, date, information, category, places, image, validated FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
