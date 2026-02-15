<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260215150139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'we need to order printing of the cards and assign them to an order';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order ADD is_print_ordered BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE card_order ADD card_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order DROP is_print_ordered');
        $this->addSql('ALTER TABLE card_order DROP card_id');
    }
}
