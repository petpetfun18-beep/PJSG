<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014084553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shoe ADD brandname VARCHAR(255) NOT NULL, ADD color VARCHAR(255) NOT NULL, ADD size DOUBLE PRECISION NOT NULL, DROP brand, DROP image, DROP description, DROP sizes, DROP category, CHANGE name name VARCHAR(255) NOT NULL, CHANGE price price DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shoe ADD brand VARCHAR(100) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD sizes JSON DEFAULT NULL, ADD category VARCHAR(100) DEFAULT NULL, DROP brandname, DROP color, DROP size, CHANGE name name VARCHAR(100) NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL');
    }
}
