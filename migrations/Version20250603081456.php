<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603081456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE author (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book (id SERIAL NOT NULL, title TEXT NOT NULL, year VARCHAR(4) DEFAULT NULL, description TEXT DEFAULT NULL, cover VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book_author (book_id INT NOT NULL, author_id INT NOT NULL, PRIMARY KEY(book_id, author_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9478D34516A2B381 ON book_author (book_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9478D345F675F31B ON book_author (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book_theme (book_id INT NOT NULL, theme_id INT NOT NULL, PRIMARY KEY(book_id, theme_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_99B7F27116A2B381 ON book_theme (book_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_99B7F27159027487 ON book_theme (theme_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE book_copy (id SERIAL NOT NULL, book_id INT NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5427F08A16A2B381 ON book_copy (book_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE borrowing (id SERIAL NOT NULL, borrowed_by_id INT NOT NULL, book_copy_id INT NOT NULL, borrow_date DATE NOT NULL, due_date DATE NOT NULL, return_date DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_226E589739759382 ON borrowing (borrowed_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_226E58973B550FE4 ON borrowing (book_copy_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE theme (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_theme ADD CONSTRAINT FK_99B7F27116A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_theme ADD CONSTRAINT FK_99B7F27159027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_copy ADD CONSTRAINT FK_5427F08A16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE borrowing ADD CONSTRAINT FK_226E589739759382 FOREIGN KEY (borrowed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE borrowing ADD CONSTRAINT FK_226E58973B550FE4 FOREIGN KEY (book_copy_id) REFERENCES book_copy (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_theme DROP CONSTRAINT FK_99B7F27116A2B381
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_theme DROP CONSTRAINT FK_99B7F27159027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE book_copy DROP CONSTRAINT FK_5427F08A16A2B381
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE borrowing DROP CONSTRAINT FK_226E589739759382
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE borrowing DROP CONSTRAINT FK_226E58973B550FE4
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE author
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book_author
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book_theme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE book_copy
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE borrowing
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE theme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
