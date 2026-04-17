<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417085535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE items (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, starting_price NUMERIC(10, 2) NOT NULL, status VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE items_category (items_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_7137A90F6BB0AE84 (items_id), INDEX IDX_7137A90F12469DE2 (category_id), PRIMARY KEY (items_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE items_category ADD CONSTRAINT FK_7137A90F6BB0AE84 FOREIGN KEY (items_id) REFERENCES items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE items_category ADD CONSTRAINT FK_7137A90F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items_category DROP FOREIGN KEY FK_7137A90F6BB0AE84');
        $this->addSql('ALTER TABLE items_category DROP FOREIGN KEY FK_7137A90F12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE items');
        $this->addSql('DROP TABLE items_category');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
