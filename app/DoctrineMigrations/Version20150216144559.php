<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216144559 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `issue_collaborators` (`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY, `issue_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, CONSTRAINT `issue_collaborators_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`), CONSTRAINT `issue_collaborators_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) )');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    	$this->addSql('ALTER TABLE `issue_collaborators` DROP FOREIGN KEY `issue_collaborators_ibfk_1`');
    	$this->addSql('ALTER TABLE `issue_collaborators` DROP FOREIGN KEY `issue_collaborators_ibfk_2`');
    	$this->addSql('DROP TABLE `issue_collaborators`');
    }
}
