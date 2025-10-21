<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251015080100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD shoe_id INT NOT NULL, ADD customer_name VARCHAR(255) NOT NULL, ADD customer_email VARCHAR(255) NOT NULL, ADD customer_address VARCHAR(255) NOT NULL, DROP costumer_name, DROP costumer_email, DROP costumer_address');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993982AD16370 FOREIGN KEY (shoe_id) REFERENCES shoe (id)');
        $this->addSql('CREATE INDEX IDX_F52993982AD16370 ON `order` (shoe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993982AD16370');
        $this->addSql('DROP INDEX IDX_F52993982AD16370 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD costumer_name VARCHAR(255) NOT NULL, ADD costumer_email VARCHAR(255) NOT NULL, ADD costumer_address VARCHAR(255) NOT NULL, DROP shoe_id, DROP customer_name, DROP customer_email, DROP customer_address');
    }
}
