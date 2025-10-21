<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014133909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shoe ADD size VARCHAR(10) DEFAULT NULL, DROP sizes, CHANGE name name VARCHAR(100) NOT NULL, CHANGE brand brand VARCHAR(100) NOT NULL, CHANGE color color VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shoe ADD sizes JSON DEFAULT NULL, DROP size, CHANGE name name VARCHAR(255) NOT NULL, CHANGE brand brand VARCHAR(255) NOT NULL, CHANGE color color VARCHAR(100) NOT NULL');
    }
}
