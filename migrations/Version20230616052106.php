<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616052106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussions ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE discussions ADD CONSTRAINT FK_8B716B63F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8B716B63F675F31B ON discussions (author_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498DDB4304');
        $this->addSql('DROP INDEX IDX_8D93D6498DDB4304 ON user');
        $this->addSql('ALTER TABLE user DROP discussions_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussions DROP FOREIGN KEY FK_8B716B63F675F31B');
        $this->addSql('DROP INDEX IDX_8B716B63F675F31B ON discussions');
        $this->addSql('ALTER TABLE discussions DROP author_id');
        $this->addSql('ALTER TABLE user ADD discussions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498DDB4304 FOREIGN KEY (discussions_id) REFERENCES discussions (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6498DDB4304 ON user (discussions_id)');
    }
}
