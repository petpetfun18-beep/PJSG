<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212052303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_shoe (category_id INT NOT NULL, shoe_id INT NOT NULL, INDEX IDX_4A2CD20E12469DE2 (category_id), INDEX IDX_4A2CD20E2AD16370 (shoe_id), PRIMARY KEY(category_id, shoe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_shoe ADD CONSTRAINT FK_4A2CD20E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_shoe ADD CONSTRAINT FK_4A2CD20E2AD16370 FOREIGN KEY (shoe_id) REFERENCES shoe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_shoe DROP FOREIGN KEY FK_4A2CD20E12469DE2');
        $this->addSql('ALTER TABLE category_shoe DROP FOREIGN KEY FK_4A2CD20E2AD16370');
        $this->addSql('DROP TABLE category_shoe');
    }
}
