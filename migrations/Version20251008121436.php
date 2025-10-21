<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251008121436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Updated Shoe table with new columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE shoe 
            ADD brand VARCHAR(100) NOT NULL, 
            ADD price DOUBLE PRECISION NOT NULL, 
            ADD size INT NOT NULL, 
            ADD image VARCHAR(255) DEFAULT NULL, 
            ADD description LONGTEXT DEFAULT NULL, 
            DROP text, 
            CHANGE name name VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE shoe 
            ADD text DOUBLE PRECISION DEFAULT NULL, 
            DROP brand, DROP price, DROP size, DROP image, DROP description, 
            CHANGE name name VARCHAR(50) NOT NULL');
    }
}
