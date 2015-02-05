<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150205162301 extends AbstractMigration
{
	public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO `issue_priority` (`id`, `label`, `order`) VALUES (1,  \'Trivial\', 10)');
        $this->addSql('INSERT INTO `issue_priority` (`id`, `label`, `order`) VALUES (2,  \'Minor\', 20)');
        $this->addSql('INSERT INTO `issue_priority` (`id`, `label`, `order`) VALUES (3,  \'Major\', 30)');
        $this->addSql('INSERT INTO `issue_priority` (`id`, `label`, `order`) VALUES (4,  \'Critical\', 40)');
        $this->addSql('INSERT INTO `issue_priority` (`id`, `label`, `order`) VALUES (5,  \'Blocker\', 50)');

        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (1, \'Fixed\', 10)');
        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (2, \'Duplicate\', 20)');
        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (3, \'Won\\\'t Fix\', 30)');
        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (4, \'Incomplete\', 40)');
        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (5, \'Cannot Reproduce\', 50)');
        $this->addSql('INSERT INTO `issue_resolution` (`id`, `label`, `order`) VALUES (6, \'Works as designed\', 60)');

        $this->addSql('INSERT INTO `issue_status` (`id`, `label`, `order`) VALUES (1, \'Open\', 10)');
        $this->addSql('INSERT INTO `issue_status` (`id`, `label`, `order`) VALUES (2, \'In progress\', 20)');
        $this->addSql('INSERT INTO `issue_status` (`id`, `label`, `order`) VALUES (3, \'Closed\', 30)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `issue_priority` WHERE `id` in (1, 2, 3, 4, 5)');

        $this->addSql('DELETE FROM `issue_resolution` WHERE `id` in (1, 2, 3, 4, 5, 6)');

        $this->addSql('DELETE FROM `issue_status` WHERE `id` in (1, 2, 3)');
    }
}
