<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616052628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussions DROP FOREIGN KEY FK_8B716B63BB208D73');
        $this->addSql('DROP INDEX IDX_8B716B63BB208D73 ON discussions');
        $this->addSql('ALTER TABLE discussions CHANGE id_tricks_id trick_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discussions ADD CONSTRAINT FK_8B716B63B281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_8B716B63B281BE2E ON discussions (trick_id)');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A3B153154');
        $this->addSql('DROP INDEX IDX_E01FBE6A3B153154 ON images');
        $this->addSql('ALTER TABLE images CHANGE tricks_id trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB281BE2E FOREIGN KEY (trick_id) REFERENCES tricks (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AB281BE2E ON images (trick_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussions DROP FOREIGN KEY FK_8B716B63B281BE2E');
        $this->addSql('DROP INDEX IDX_8B716B63B281BE2E ON discussions');
        $this->addSql('ALTER TABLE discussions CHANGE trick_id id_tricks_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discussions ADD CONSTRAINT FK_8B716B63BB208D73 FOREIGN KEY (id_tricks_id) REFERENCES tricks (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8B716B63BB208D73 ON discussions (id_tricks_id)');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB281BE2E');
        $this->addSql('DROP INDEX IDX_E01FBE6AB281BE2E ON images');
        $this->addSql('ALTER TABLE images CHANGE trick_id tricks_id INT NOT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A3B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E01FBE6A3B153154 ON images (tricks_id)');
    }
}
