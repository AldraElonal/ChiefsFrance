<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191104151738 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE url_picture image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e669d86650f TO IDX_23A0E66A76ED395');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e669777d11e TO IDX_23A0E6612469DE2');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE image_name url_picture VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e6612469de2 TO IDX_23A0E669777D11E');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e66a76ed395 TO IDX_23A0E669D86650F');
    }
}
