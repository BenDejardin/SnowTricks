<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616051138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD tricks_id INT NOT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A3B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6A3B153154 ON images (tricks_id)');
        $this->addSql('ALTER TABLE videos ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA6432B281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_29AA6432B281BE2E ON videos (trick_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A3B153154');
        $this->addSql('DROP INDEX IDX_E01FBE6A3B153154 ON images');
        $this->addSql('ALTER TABLE images DROP tricks_id');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA6432B281BE2E');
        $this->addSql('DROP INDEX IDX_29AA6432B281BE2E ON videos');
        $this->addSql('ALTER TABLE videos DROP trick_id');
    }
}
