<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015070451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, storage_id INT DEFAULT NULL, status_id INT DEFAULT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(2000) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, stock_quantity INT NOT NULL, INDEX IDX_D338D5835CC5DB90 (storage_id), INDEX IDX_D338D5836BF700BD (status_id), INDEX IDX_D338D58312469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lane (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, building_id INT NOT NULL, lane_id INT DEFAULT NULL, INDEX IDX_547A1B344D2A7E12 (building_id), INDEX IDX_547A1B34A128F72F (lane_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5835CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id)');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D5836BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D58312469DE2 FOREIGN KEY (category_id) REFERENCES equipment_category (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B344D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B34A128F72F FOREIGN KEY (lane_id) REFERENCES lane (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5835CC5DB90');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D5836BF700BD');
        $this->addSql('ALTER TABLE equipment DROP FOREIGN KEY FK_D338D58312469DE2');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B344D2A7E12');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B34A128F72F');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE equipment_category');
        $this->addSql('DROP TABLE lane');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE storage');
    }
}
