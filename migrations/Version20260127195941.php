<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260127195941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'move mail to user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order DROP mail');
        $this->addSql('ALTER TABLE "user" ADD mail VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_order ADD mail VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP mail');
    }
}
