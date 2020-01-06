<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200106094730 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audio_file (id INT AUTO_INCREMENT NOT NULL, artist_id INT NOT NULL, album_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, md5 VARCHAR(255) NOT NULL, track_number INT DEFAULT NULL, compilation TINYINT(1) NOT NULL, playtime INT DEFAULT NULL, codec VARCHAR(255) DEFAULT NULL, bitrate INT DEFAULT NULL, channels INT DEFAULT NULL, bit_depth INT DEFAULT NULL, file_size INT NOT NULL, audio TINYINT(1) NOT NULL, mime_type VARCHAR(255) NOT NULL, INDEX IDX_C32E2A4CB7970CF8 (artist_id), INDEX IDX_C32E2A4C1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, artist_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, compilation TINYINT(1) NOT NULL, normalized_name VARCHAR(255) NOT NULL, INDEX IDX_39986E43B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, normalized_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audio_file ADD CONSTRAINT FK_C32E2A4CB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE audio_file ADD CONSTRAINT FK_C32E2A4C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE audio_file DROP FOREIGN KEY FK_C32E2A4C1137ABCF');
        $this->addSql('ALTER TABLE audio_file DROP FOREIGN KEY FK_C32E2A4CB7970CF8');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43B7970CF8');
        $this->addSql('DROP TABLE audio_file');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE artist');
    }
}
