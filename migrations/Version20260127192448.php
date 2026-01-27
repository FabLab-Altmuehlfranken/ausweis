<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260127192448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'prepare database for ordering cards';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE card_order (id SERIAL NOT NULL, requested_by_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, mail VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD1D625E4DA1E751 ON card_order (requested_by_id)');
        $this->addSql('COMMENT ON COLUMN card_order.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE card_order ADD CONSTRAINT FK_CD1D625E4DA1E751 FOREIGN KEY (requested_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD display_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD digital_card_id UUID NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD card_id BIGINT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".digital_card_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE card_order DROP CONSTRAINT FK_CD1D625E4DA1E751');
        $this->addSql('DROP TABLE card_order');
        $this->addSql('ALTER TABLE "user" DROP display_name');
        $this->addSql('ALTER TABLE "user" DROP digital_card_id');
        $this->addSql('ALTER TABLE "user" DROP card_id');
    }
}
