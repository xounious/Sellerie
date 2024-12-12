<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210131252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD borrower_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09E8D88425 FOREIGN KEY (borrower_id_id) REFERENCES borrower (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09E8D88425 ON customer (borrower_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09E8D88425');
        $this->addSql('DROP INDEX UNIQ_81398E09E8D88425 ON customer');
        $this->addSql('ALTER TABLE customer DROP borrower_id_id');
    }
}
