-- Création de la base de données
CREATE DATABASE IF NOT EXISTS boutique_en_ligne;
USE boutique_en_ligne;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    adresse VARCHAR(255),
    telephone VARCHAR(15),
    est_admin BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE IF NOT EXISTS produits (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categorie VARCHAR(50),
    image_url VARCHAR(255),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create a table for storing product images in the database
CREATE TABLE IF NOT EXISTS produit_images (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_produit INT NOT NULL,
    image_data MEDIUMBLOB NOT NULL,
    image_type VARCHAR(50) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    titre VARCHAR(100),
    ordre INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des paniers
CREATE TABLE IF NOT EXISTS paniers (
    id_panier INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table des éléments du panier
CREATE TABLE IF NOT EXISTS elements_panier (
    id_element INT AUTO_INCREMENT PRIMARY KEY,
    id_panier INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_panier) REFERENCES paniers(id_panier) ON DELETE CASCADE,
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'validee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table des détails de commande
CREATE TABLE IF NOT EXISTS details_commande (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE,
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE
);

-- Table historique des commandes annulées
CREATE TABLE IF NOT EXISTS commandes_annulees (
    id_annulation INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_utilisateur INT NOT NULL,
    date_annulation DATETIME DEFAULT CURRENT_TIMESTAMP,
    raison TEXT,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table historique des détails de commandes annulées
CREATE TABLE IF NOT EXISTS details_commandes_annulees (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_annulation INT NOT NULL,
    id_produit INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_annulation) REFERENCES commandes_annulees(id_annulation) ON DELETE CASCADE,
    FOREIGN KEY (id_produit) REFERENCES produits(id_produit) ON DELETE CASCADE
);