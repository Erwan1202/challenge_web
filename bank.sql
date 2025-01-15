-- Créaton de la base de données

-- Utilisation de la base de données    

USE banque;


-- Création de la table Utilisateurs
CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    telephone VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    mdp_chiffre TEXT NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('client', 'admin'))
);

-- Création de la table Compte Bancaire
CREATE TABLE comptes_bancaires (
    id SERIAL PRIMARY KEY,
    numero_de_compte VARCHAR(20) NOT NULL UNIQUE,
    type_compte VARCHAR(10) NOT NULL CHECK (type_compte IN ('epargne', 'courant')),
    solde NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    decouvert_autorise NUMERIC(12, 2) DEFAULT 200.00,
    utilisateur_id INTEGER NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Création de la table Transactions
CREATE TABLE transactions (
    id SERIAL PRIMARY KEY,
    type_transaction VARCHAR(10) NOT NULL CHECK (type_transaction IN ('depot', 'retrait', 'virement')),
    montant NUMERIC(12, 2) NOT NULL,
    date_heure TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(10) NOT NULL CHECK (statut IN ('reussi', 'annule')),
    compte_source_id INTEGER,
    compte_dest_id INTEGER,
    FOREIGN KEY (compte_source_id) REFERENCES comptes_bancaires(id),
    FOREIGN KEY (compte_dest_id) REFERENCES comptes_bancaires(id)
);
