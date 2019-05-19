<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190515171212 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO `consumer` (`id`, `name`, `description`, `process_number`, `status`) VALUES (1, 'device', NULL, '2', '1');");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM consumer WHERE id = 1");
    }
}
