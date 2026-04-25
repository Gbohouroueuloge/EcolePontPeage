CREATE TABLE Guichet (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(50) NOT NULL,
    emplacement VARCHAR(150) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id)
);


CREATE TABLE Users (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password TEXT NOT NULL,
    role ENUM('operateur', 'admin') NOT NULL DEFAULT 'operateur',
    is_active BOOLEAN NOT NULL DEFAULT true,
    last_login_at TIMESTAMP(3),
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),

    PRIMARY KEY (id)
);


CREATE TABLE Agent (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    guichet_id INT UNSIGNED NOT NULL,
    debut DATETIME NULL,
    fin DATETIME NULL,
    date_assignation DATETIME NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id),

    UNIQUE (user_id),

    CONSTRAINT fk_agent
        FOREIGN KEY (user_id)
        REFERENCES Users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_agent_guichet
        FOREIGN KEY (guichet_id)
        REFERENCES Guichet(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE TypeVehicule (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    libelle VARCHAR(150) NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id)
);


CREATE TABLE Vehicule (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    immatriculation VARCHAR(50) NOT NULL,
    type_vehicule_id INT UNSIGNED NOT NULL,
    marque VARCHAR(50) NULL,
    modele VARCHAR(50) NULL,
    couleur VARCHAR(50) NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    
    PRIMARY KEY (id),
    
    -- Définition de la relation
    CONSTRAINT fk_vehicule_type 
        FOREIGN KEY (type_vehicule_id) 
        REFERENCES TypeVehicule(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE Paiement (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicule_id INT UNSIGNED NOT NULL,
    guichet_id INT UNSIGNED NOT NULL,
    mode_paiement VARCHAR(50) NOT NULL,
    montant DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_valide BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id),

    CONSTRAINT fk_paiement_vehicule
        FOREIGN KEY (vehicule_id)
        REFERENCES Vehicule(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_paiement_guichet
        FOREIGN KEY (guichet_id)
        REFERENCES Guichet(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE Incident (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    vehicule_id INT UNSIGNED NOT NULL,
    guichet_id INT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NULL,
    url_image VARCHAR(255) NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id),

    CONSTRAINT fk_incident_vehicule
        FOREIGN KEY (vehicule_id)
        REFERENCES Vehicule(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_incident_guichet
        FOREIGN KEY (guichet_id)
        REFERENCES Guichet(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE Abonnement (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    description TEXT NULL,
    type VARCHAR(50) NOT NULL,
    avantages TEXT NOT NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id),
);


CREATE TABLE Abonnement_Vehicule (
    abonnement_id INT UNSIGNED NOT NULL,
    vehicule_id INT UNSIGNED NOT NULL,
    expires_at TIMESTAMP(3) NOT NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (abonnement_id, vehicule_id),

    CONSTRAINT fk_abonnement_vehicule_abonnement
        FOREIGN KEY (abonnement_id)
        REFERENCES Abonnement(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_abonnement_vehicule_vehicule
        FOREIGN KEY (vehicule_id)
        REFERENCES Vehicule(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- Insertion de données

INSERT INTO `guichet` (`slug`, `emplacement`) VALUES
('voie-01-nord', 'Nord'),
('voie-02-sud', 'Sud'),
('voie-03-ouest', 'Ouest'),
('voie-04-est', 'Est'),
('voie-05-nord-est', 'Nord-Est'),
('voie-06-nord-ouest', 'Nord-Ouest'),
('voie-07-sud-est', 'Sud-Est'),
('voie-08-sud-ouest', 'Sud-Ouest');


INSERT INTO `agent` (`user_id`, `guichet_id`) 
VALUES (1, 2),