<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204192432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier (idPanier INT AUTO_INCREMENT NOT NULL, etat INT NOT NULL, userId INT NOT NULL, prix INT DEFAULT NULL, PRIMARY KEY(idPanier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (idProd VARCHAR(255) NOT NULL, nomProd TEXT NOT NULL, descriptionProd TEXT NOT NULL, prixProd INT NOT NULL, remise DOUBLE PRECISION NOT NULL, imageProd VARCHAR(155) NOT NULL, PRIMARY KEY(idProd)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produit');
    }
}
