<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230830073551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates all initial tables for employees vacation time tracking system';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE role (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT
            )'
        );
        $this->addSql(
            '
            INSERT INTO role(id, name)
            VALUES
                (1, "ProjectLead"),
                (2, "TeamLead"),
                (3, "Member")
            '
        );
        $this->addSql(
            '
            CREATE INDEX role_name_index
            ON role(name)
            '
        );

        $this->addSql(
            '
            CREATE TABLE workplace (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name TEXT NOT NULL,
                description TEXT
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE user (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL,
                workplace_id INT REFERENCES workplace(id),
                is_admin BOOLEAN NOT NULL
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE vacation_request (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL REFERENCES user(id),
                from_date DATE NOT NULL,
                to_date DATE NOT NULL,
                created_at DATETIME,
                updated_at DATETIME
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE vacation_request_approval (
                id INT AUTO_INCREMENT PRIMARY KEY,
                approved_by_team_lead_id INT NOT NULL REFERENCES user(id),
                approved_by_project_lead_id INT NOT NULL REFERENCES user(id),
                is_approved_by_team_lead BOOLEAN,
                is_approved_by_project_lead BOOLEAN,
                approval_status_team_updated_at DATETIME,
                approval_status_project_updated_at DATETIME,
                created_at DATETIME,
                updated_at DATETIME
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE annual_vacation (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT REFERENCES user(id),
                year YEAR,
                maximum_vacation_days INT NOT NULL,
                vacation_days_taken INT NOT NULL,
                created_at DATETIME,
                updated_at DATETIME,
                UNIQUE (user_id, year)
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE project (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_lead INT NOT NULL REFERENCES user(id),
                name TEXT NOT NULL,
                description TEXT
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE team (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name TEXT NOT NULL,
                description TEXT
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE team_member (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INT REFERENCES user(id),
                team_id INT REFERENCES team(id),
                UNIQUE (member_id, team_id)
            )
            '
        );

        $this->addSql(
            '
            CREATE TABLE project_team (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT REFERENCES project(id),
                team_id INT REFERENCES team(id),
                UNIQUE (project_id, team_id)
            )
            '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS project_team');
        $this->addSql('DROP TABLE IF EXISTS team_member');
        $this->addSql('DROP TABLE IF EXISTS team');
        $this->addSql('DROP TABLE IF EXISTS project');
        $this->addSql('DROP TABLE IF EXISTS annual_vacation');
        $this->addSql('DROP TABLE IF EXISTS vacation_request');
        $this->addSql('DROP TABLE IF EXISTS user');
        $this->addSql('DROP TABLE IF EXISTS workplace');
        $this->addSql('DROP TABLE IF EXISTS role');
    }
}
