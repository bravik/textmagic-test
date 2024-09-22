<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240922030510 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE questions (id UUID NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN questions.id IS \'(DC2Type:common__id)\'');

        $this->addSql('CREATE TABLE questions_choices (id UUID NOT NULL, question_id UUID DEFAULT NULL, text VARCHAR(255) NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_764BB6FF1E27F6BF ON questions_choices (question_id)');
        $this->addSql('COMMENT ON COLUMN questions_choices.id IS \'(DC2Type:common__id)\'');
        $this->addSql('COMMENT ON COLUMN questions_choices.question_id IS \'(DC2Type:common__id)\'');

        $this->addSql('CREATE TABLE tests (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tests.id IS \'(DC2Type:common__id)\'');
        $this->addSql('CREATE TABLE tests_questions (id UUID NOT NULL, test_id UUID DEFAULT NULL, question_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_30F55EE11E5D0459 ON tests_questions (test_id)');
        $this->addSql('COMMENT ON COLUMN tests_questions.id IS \'(DC2Type:common__id)\'');
        $this->addSql('COMMENT ON COLUMN tests_questions.test_id IS \'(DC2Type:common__id)\'');
        $this->addSql('COMMENT ON COLUMN tests_questions.question_id IS \'(DC2Type:common__id)\'');

        $this->addSql('ALTER TABLE questions_choices ADD CONSTRAINT FK_764BB6FF1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tests_questions ADD CONSTRAINT FK_30F55EE11E5D0459 FOREIGN KEY (test_id) REFERENCES tests (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('CREATE TABLE submissions (id UUID NOT NULL, test_id UUID NOT NULL, data JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN submissions.id IS \'(DC2Type:common__id)\'');
        $this->addSql('COMMENT ON COLUMN submissions.test_id IS \'(DC2Type:common__id)\'');
        $this->addSql('COMMENT ON COLUMN submissions.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE submissions');
        $this->addSql('ALTER TABLE questions_choices DROP CONSTRAINT FK_764BB6FF1E27F6BF');
        $this->addSql('ALTER TABLE tests_questions DROP CONSTRAINT FK_30F55EE11E5D0459');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE questions_choices');
        $this->addSql('DROP TABLE tests');
        $this->addSql('DROP TABLE tests_questions');
    }
}
