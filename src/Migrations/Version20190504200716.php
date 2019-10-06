<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190504200716 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
        $this->addSql('ALTER TABLE collection ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
        $this->addSql('ALTER TABLE item_collections ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
        $this->addSql('ALTER TABLE category ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
        $this->addSql('ALTER TABLE item_categories ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE item DROP created_at, DROP updated_at, DROP deleted_at;');
        $this->addSql('ALTER TABLE collection DROP created_at, DROP updated_at, DROP deleted_at;');
        $this->addSql('ALTER TABLE item_collectiond DROP created_at, DROP updated_at, DROP deleted_at;');
        $this->addSql('ALTER TABLE category DROP created_at, DROP updated_at, DROP deleted_at;');
        $this->addSql('ALTER TABLE item_categories DROP created_at, DROP updated_at, DROP deleted_at;');
    }
}
