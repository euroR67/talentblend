<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218090251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contrat (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE emploi (id INT AUTO_INCREMENT NOT NULL, categories_id INT NOT NULL, contrats_id INT NOT NULL, ville_id INT NOT NULL, niveau_id INT NOT NULL, entreprise_id INT NOT NULL, types_id INT NOT NULL, user_id INT NOT NULL, poste VARCHAR(40) NOT NULL, disponibilite VARCHAR(40) NOT NULL, description LONGTEXT NOT NULL, salaire INT NOT NULL, date_offre DATE NOT NULL, INDEX IDX_74A0B0FAA21214B7 (categories_id), INDEX IDX_74A0B0FA6A6193D6 (contrats_id), INDEX IDX_74A0B0FAA73F0036 (ville_id), INDEX IDX_74A0B0FAB3E9C81 (niveau_id), INDEX IDX_74A0B0FAA4AEAFEA (entreprise_id), INDEX IDX_74A0B0FA8EB23357 (types_id), INDEX IDX_74A0B0FAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, ville_id INT NOT NULL, tailles_id INT DEFAULT NULL, user_id INT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, raison_social VARCHAR(40) NOT NULL, secteur VARCHAR(30) NOT NULL, website VARCHAR(255) DEFAULT NULL, banniere VARCHAR(255) DEFAULT NULL, kbis VARCHAR(255) NOT NULL, is_verified TINYINT(1) DEFAULT NULL, motif_refus VARCHAR(255) DEFAULT NULL, date_creation DATE DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_D19FA60A73F0036 (ville_id), INDEX IDX_D19FA601AEC613E (tailles_id), INDEX IDX_D19FA60A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE experience (id INT AUTO_INCREMENT NOT NULL, user_exp_id INT NOT NULL, titre VARCHAR(50) NOT NULL, entreprise VARCHAR(40) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, description LONGTEXT NOT NULL, INDEX IDX_590C103B73433C7 (user_exp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, user_formation_id INT NOT NULL, titre VARCHAR(40) NOT NULL, qualification VARCHAR(40) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, description LONGTEXT NOT NULL, INDEX IDX_404021BFD2CC542C (user_formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langue (id INT AUTO_INCREMENT NOT NULL, langage VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, text_message VARCHAR(255) NOT NULL, date_message DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metier (id INT AUTO_INCREMENT NOT NULL, nom_metier VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE niveau (id INT AUTO_INCREMENT NOT NULL, annee_experience VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postule (id INT AUTO_INCREMENT NOT NULL, user_postulant_id INT DEFAULT NULL, emploi_id INT DEFAULT NULL, date_postulation DATE NOT NULL, status TINYINT(1) DEFAULT NULL, INDEX IDX_742304C99C1F727A (user_postulant_id), INDEX IDX_742304C9EC013E12 (emploi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE represente (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, user_entreprise_id INT DEFAULT NULL, status TINYINT(1) DEFAULT NULL, motif_refus VARCHAR(255) DEFAULT NULL, kbis VARCHAR(255) DEFAULT NULL, INDEX IDX_E146562A4AEAFEA (entreprise_id), INDEX IDX_E1465624A2002BA (user_entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taille (id INT AUTO_INCREMENT NOT NULL, taille VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_emploi (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, metiers_id INT DEFAULT NULL, niveau_id INT DEFAULT NULL, villes_id INT DEFAULT NULL, messages_envoyes_id INT DEFAULT NULL, messages_recus_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, pays VARCHAR(30) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649DCF28B17 (metiers_id), INDEX IDX_8D93D649B3E9C81 (niveau_id), INDEX IDX_8D93D649286C17BC (villes_id), INDEX IDX_8D93D649D45ED6F3 (messages_envoyes_id), INDEX IDX_8D93D6491C198BD5 (messages_recus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_langue (user_id INT NOT NULL, langue_id INT NOT NULL, INDEX IDX_F6056EB3A76ED395 (user_id), INDEX IDX_F6056EB32AADBACD (langue_id), PRIMARY KEY(user_id, langue_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_emploi (user_id INT NOT NULL, emploi_id INT NOT NULL, INDEX IDX_11F2ABC7A76ED395 (user_id), INDEX IDX_11F2ABC7EC013E12 (emploi_id), PRIMARY KEY(user_id, emploi_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ville (id INT AUTO_INCREMENT NOT NULL, nom_ville VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAA21214B7 FOREIGN KEY (categories_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA6A6193D6 FOREIGN KEY (contrats_id) REFERENCES contrat (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAA73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FA8EB23357 FOREIGN KEY (types_id) REFERENCES type_emploi (id)');
        $this->addSql('ALTER TABLE emploi ADD CONSTRAINT FK_74A0B0FAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA601AEC613E FOREIGN KEY (tailles_id) REFERENCES taille (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE experience ADD CONSTRAINT FK_590C103B73433C7 FOREIGN KEY (user_exp_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFD2CC542C FOREIGN KEY (user_formation_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE postule ADD CONSTRAINT FK_742304C99C1F727A FOREIGN KEY (user_postulant_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE postule ADD CONSTRAINT FK_742304C9EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id)');
        $this->addSql('ALTER TABLE represente ADD CONSTRAINT FK_E146562A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE represente ADD CONSTRAINT FK_E1465624A2002BA FOREIGN KEY (user_entreprise_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649DCF28B17 FOREIGN KEY (metiers_id) REFERENCES metier (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649286C17BC FOREIGN KEY (villes_id) REFERENCES ville (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649D45ED6F3 FOREIGN KEY (messages_envoyes_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6491C198BD5 FOREIGN KEY (messages_recus_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE user_langue ADD CONSTRAINT FK_F6056EB3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_langue ADD CONSTRAINT FK_F6056EB32AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_emploi ADD CONSTRAINT FK_11F2ABC7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_emploi ADD CONSTRAINT FK_11F2ABC7EC013E12 FOREIGN KEY (emploi_id) REFERENCES emploi (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAA21214B7');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA6A6193D6');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAA73F0036');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAB3E9C81');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAA4AEAFEA');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FA8EB23357');
        $this->addSql('ALTER TABLE emploi DROP FOREIGN KEY FK_74A0B0FAA76ED395');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60A73F0036');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA601AEC613E');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60A76ED395');
        $this->addSql('ALTER TABLE experience DROP FOREIGN KEY FK_590C103B73433C7');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BFD2CC542C');
        $this->addSql('ALTER TABLE postule DROP FOREIGN KEY FK_742304C99C1F727A');
        $this->addSql('ALTER TABLE postule DROP FOREIGN KEY FK_742304C9EC013E12');
        $this->addSql('ALTER TABLE represente DROP FOREIGN KEY FK_E146562A4AEAFEA');
        $this->addSql('ALTER TABLE represente DROP FOREIGN KEY FK_E1465624A2002BA');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649DCF28B17');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649B3E9C81');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649286C17BC');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649D45ED6F3');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6491C198BD5');
        $this->addSql('ALTER TABLE user_langue DROP FOREIGN KEY FK_F6056EB3A76ED395');
        $this->addSql('ALTER TABLE user_langue DROP FOREIGN KEY FK_F6056EB32AADBACD');
        $this->addSql('ALTER TABLE user_emploi DROP FOREIGN KEY FK_11F2ABC7A76ED395');
        $this->addSql('ALTER TABLE user_emploi DROP FOREIGN KEY FK_11F2ABC7EC013E12');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE contrat');
        $this->addSql('DROP TABLE emploi');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE experience');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE langue');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE metier');
        $this->addSql('DROP TABLE niveau');
        $this->addSql('DROP TABLE postule');
        $this->addSql('DROP TABLE represente');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE taille');
        $this->addSql('DROP TABLE type_emploi');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_langue');
        $this->addSql('DROP TABLE user_emploi');
        $this->addSql('DROP TABLE ville');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
