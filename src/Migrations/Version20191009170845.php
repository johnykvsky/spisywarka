<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009170845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');    
        $this->addSql('DROP TABLE item_categories');
        $this->addSql('ALTER TABLE item ADD category_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');    
        $this->addSql('ALTER TABLE item DROP category_id;');
        $this->addSql('CREATE TABLE item_categories (item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', category_id  CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY (item_id, category_id) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT fk_item_categories_item_id FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT fk_item_categories_category_id FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT unique_item_category UNIQUE (item_id, category_id)');
    }
}
