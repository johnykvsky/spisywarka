<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190910184740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item_collections ADD CONSTRAINT fk_collections_item_id FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item_collections ADD CONSTRAINT fk_collections_collection_id FOREIGN KEY (collection_id) REFERENCES collection (id)');
        $this->addSql('ALTER TABLE item_collections ADD CONSTRAINT unique_item_collection UNIQUE (item_id, collection_id)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item_collections DROP FOREIGN KEY fk_collections_item_id');
        $this->addSql('ALTER TABLE item_collections DROP FOREIGN KEY fk_collections_collection_id');
        $this->addSql('ALTER TABLE item_collections DROP FOREIGN KEY unique_item_collection');
    }
}
