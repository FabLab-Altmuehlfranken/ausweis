<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202135801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order DROP CONSTRAINT fk_cd1d625e4da1e751');
        $this->addSql('DROP INDEX uniq_cd1d625e4da1e751');
        $this->addSql('ALTER TABLE card_order RENAME COLUMN requested_by_id TO "user"');
        $this->addSql('ALTER TABLE card_order ADD CONSTRAINT FK_CD1D625E356B3608 FOREIGN KEY ("user") REFERENCES "user" ("id") NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD1D625E356B3608 ON card_order ("user")');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496A4E412A ON "user" (digital_card_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6494ACC9A20 ON "user" (card_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order DROP CONSTRAINT FK_CD1D625E356B3608');
        $this->addSql('DROP INDEX UNIQ_CD1D625E356B3608');
        $this->addSql('ALTER TABLE card_order RENAME COLUMN "user" TO requested_by_id');
        $this->addSql('ALTER TABLE card_order ADD CONSTRAINT fk_cd1d625e4da1e751 FOREIGN KEY (requested_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_cd1d625e4da1e751 ON card_order (requested_by_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6496A4E412A');
        $this->addSql('DROP INDEX UNIQ_8D93D6494ACC9A20');
    }
}
