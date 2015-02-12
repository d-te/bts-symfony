<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150205155313 extends AbstractMigration
{
   public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, issue_id INT DEFAULT NULL, user_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, INDEX IDX_AC74095A5E7AA58C (issue_id), INDEX IDX_AC74095AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, issue_id INT DEFAULT NULL, body VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, reporter_id INT DEFAULT NULL, project_id INT DEFAULT NULL, status_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, priority_id INT DEFAULT NULL, resolution_id INT DEFAULT NULL, summary VARCHAR(255) NOT NULL, code VARCHAR(20) DEFAULT NULL, description VARCHAR(255) NOT NULL, type INT NOT NULL, parent_id INT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_12AD233EE1CFE6F5 (reporter_id), INDEX IDX_12AD233E166D1F9C (project_id), INDEX IDX_12AD233E6BF700BD (status_id), INDEX IDX_12AD233E59EC7D60 (assignee_id), INDEX IDX_12AD233E497B19F9 (priority_id), INDEX IDX_12AD233E12A1C43A (resolution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_priority (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(20) NOT NULL, `order` INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_resolution (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(20) NOT NULL, `order` INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_status (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(20) NOT NULL, `order` INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_members (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, project_id INT DEFAULT NULL, INDEX IDX_D3BEDE9AA76ED395 (user_id), INDEX IDX_D3BEDE9A166D1F9C (project_id), UNIQUE INDEX project_id_user_id (project_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, role VARCHAR(20) NOT NULL, UNIQUE INDEX role (role), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, avatar VARCHAR(255) NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_54FCD59FD60322AC (role_id), INDEX IDX_54FCD59FA76ED395 (user_id), UNIQUE INDEX user_id_role_id (user_id, role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EE1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E6BF700BD FOREIGN KEY (status_id) REFERENCES issue_status (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E59EC7D60 FOREIGN KEY (assignee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E497B19F9 FOREIGN KEY (priority_id) REFERENCES issue_priority (id)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E12A1C43A FOREIGN KEY (resolution_id) REFERENCES issue_resolution (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A5E7AA58C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5E7AA58C');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E497B19F9');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E12A1C43A');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E6BF700BD');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E166D1F9C');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9A166D1F9C');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EE1CFE6F5');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E59EC7D60');
        $this->addSql('ALTER TABLE project_members DROP FOREIGN KEY FK_D3BEDE9AA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE issue_priority');
        $this->addSql('DROP TABLE issue_resolution');
        $this->addSql('DROP TABLE issue_status');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_roles');
    }
}
