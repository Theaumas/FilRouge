<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131135742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_projets (user_id INT NOT NULL, projets_id INT NOT NULL, INDEX IDX_EC59DFD1A76ED395 (user_id), INDEX IDX_EC59DFD1597A6CB7 (projets_id), PRIMARY KEY(user_id, projets_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_projets ADD CONSTRAINT FK_EC59DFD1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_projets ADD CONSTRAINT FK_EC59DFD1597A6CB7 FOREIGN KEY (projets_id) REFERENCES projets (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_projets DROP FOREIGN KEY FK_EC59DFD1A76ED395');
        $this->addSql('ALTER TABLE user_projets DROP FOREIGN KEY FK_EC59DFD1597A6CB7');
        $this->addSql('DROP TABLE user_projets');
    }
}
