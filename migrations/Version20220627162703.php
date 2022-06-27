<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627162703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_task (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(45) NOT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(45) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(45) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sprint (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, sprint_name VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(45) NOT NULL, updated_at DATETIME DEFAULT NULL, updated_by VARCHAR(45) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, deleted_by VARCHAR(45) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_status (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type_project (user_type_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_875EC3199D419299 (user_type_id), INDEX IDX_875EC319166D1F9C (project_id), PRIMARY KEY(user_type_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_type_project ADD CONSTRAINT FK_875EC3199D419299 FOREIGN KEY (user_type_id) REFERENCES user_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_type_project ADD CONSTRAINT FK_875EC319166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_type_project DROP FOREIGN KEY FK_875EC319166D1F9C');
        $this->addSql('ALTER TABLE user_type_project DROP FOREIGN KEY FK_875EC3199D419299');
        $this->addSql('DROP TABLE group_task');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE sprint');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_status');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE user_type_project');
    }
}
