<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229092620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wiki_image (id INT AUTO_INCREMENT NOT NULL, wiki_page_id INT DEFAULT NULL, wiki_image_name VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6AB73DD3C4759321 (wiki_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wiki_page (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wiki_image ADD CONSTRAINT FK_6AB73DD3C4759321 FOREIGN KEY (wiki_page_id) REFERENCES wiki_page (id)');
        $this->addSql('ALTER TABLE article CHANGE write_date write_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wiki_image DROP FOREIGN KEY FK_6AB73DD3C4759321');
        $this->addSql('DROP TABLE wiki_image');
        $this->addSql('DROP TABLE wiki_page');
        $this->addSql('ALTER TABLE article CHANGE write_date write_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
