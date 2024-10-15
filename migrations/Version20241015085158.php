<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015085158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan (id INT AUTO_INCREMENT NOT NULL, borrower_id INT NOT NULL, equipment_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_C5D30D0311CE312B (borrower_id), INDEX IDX_C5D30D03517FE9FE (equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D0311CE312B FOREIGN KEY (borrower_id) REFERENCES borrower (id)');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D03517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D0311CE312B');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D03517FE9FE');
        $this->addSql('DROP TABLE loan');
    }
}
