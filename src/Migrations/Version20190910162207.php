<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190910162207 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT fk_item_categories_item_id FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT fk_item_categories_category_id FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE item_categories ADD CONSTRAINT unique_item_category UNIQUE (item_id, category_id)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item_categories DROP FOREIGN KEY fk_item_categories_item_id');
        $this->addSql('ALTER TABLE item_categories DROP FOREIGN KEY fk_item_categories_category_id');
        $this->addSql('ALTER TABLE item_categories DROP FOREIGN KEY unique_item_category');
    }
}
