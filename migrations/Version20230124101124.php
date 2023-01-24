<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230124101124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D3569D950');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8D3569D950 ON post');
        $this->addSql('ALTER TABLE post ADD thumb_name VARCHAR(255) NOT NULL, DROP featured_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD featured_image_id INT DEFAULT NULL, DROP thumb_name');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D3569D950 FOREIGN KEY (featured_image_id) REFERENCES image (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8D3569D950 ON post (featured_image_id)');
    }
}
