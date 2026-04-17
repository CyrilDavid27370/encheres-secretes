<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417140418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items ADD final_price NUMERIC(10, 2) DEFAULT NULL, ADD winner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE items ADD CONSTRAINT FK_E11EE94D5DFCD4B8 FOREIGN KEY (winner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E11EE94D5DFCD4B8 ON items (winner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items DROP FOREIGN KEY FK_E11EE94D5DFCD4B8');
        $this->addSql('DROP INDEX IDX_E11EE94D5DFCD4B8 ON items');
        $this->addSql('ALTER TABLE items DROP final_price, DROP winner_id');
    }
}
