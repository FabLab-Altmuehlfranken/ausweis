<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260922192443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add revocation of user privileges';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_privilege ADD revoked_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE user_privilege ADD revocation_reason TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_privilege ADD revoked_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_privilege ADD CONSTRAINT FK_87C017638E5493E3 FOREIGN KEY (revoked_by) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_87C017638E5493E3 ON user_privilege (revoked_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_privilege DROP CONSTRAINT FK_87C017638E5493E3');
        $this->addSql('DROP INDEX IDX_87C017638E5493E3');
        $this->addSql('ALTER TABLE user_privilege DROP revoked_at');
        $this->addSql('ALTER TABLE user_privilege DROP revocation_reason');
        $this->addSql('ALTER TABLE user_privilege DROP revoked_by');
    }
}
