<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190414115049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pin (id INT AUTO_INCREMENT NOT NULL, bcm_id INT NOT NULL, physical_id INT NOT NULL, name VARCHAR(32) NOT NULL, mode VARCHAR(3) DEFAULT NULL, state INT DEFAULT NULL, status TINYINT(1) NOT NULL, description VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql("INSERT INTO `pin` (`id`, `bcm_id`, `physical_id`, `name`, `mode`, `state`, `status`, `description`) VALUES 
              (NULL, '0', '11', 'GPIO. 0', NULL, NULL, '1', NULL), (NULL, '18', '12', 'GPIO. 1', NULL, NULL, '1', NULL), 
              (NULL, '2', '13', 'GPIO. 2', NULL, NULL, '1', NULL), (NULL, '22', '15', 'GPIO. 3', NULL, NULL, '1', NULL), 
              (NULL, '23', '16', 'GPIO. 4', NULL, NULL, '1', NULL), (NULL, '24', '18', 'GPIO. 5', NULL, NULL, '1', NULL), 
              (NULL, '25', '22', 'GPIO. 6', NULL, NULL, '1', NULL), (NULL, '4', '7', 'GPIO. 7', NULL, NULL, '1', NULL), 
              (NULL, '5', '29', 'GPIO. 21', NULL, NULL, '1', NULL), (NULL, '6', '31', 'GPIO. 22', NULL, NULL, '1', NULL), 
              (NULL, '13', '33', 'GPIO. 23', NULL, NULL, '1', NULL), (NULL, '19', '35', 'GPIO. 24', NULL, NULL, '1', NULL), 
              (NULL, '26', '37', 'GPIO. 25', NULL, NULL, '1', NULL), (NULL, '12', '32', 'GPIO. 26', NULL, NULL, '1', NULL), 
              (NULL, '16', '36', 'GPIO. 27', NULL, NULL, '1', NULL), (NULL, '20', '38', 'GPIO. 28', NULL, NULL, '1', NULL), 
              (NULL, '21', '40', 'GPIO. 29', NULL, NULL, '1', NULL);");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pin');
    }
}
