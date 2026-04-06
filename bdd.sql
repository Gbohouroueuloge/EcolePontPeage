CREATE TABLE Users (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password TEXT NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'operateur',
    is_active BOOLEAN NOT NULL DEFAULT true,
    last_login_at TIMESTAMP(3),
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP(3) NOT NULL,

    PRIMARY KEY (id)
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
    type_vehicule_id INT UNSIGNED NOT NULL, -- Changé en UNSIGNED pour correspondre à l'ID
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
        ON DELETE RESTRICT -- Empêche de supprimer un type s'il est utilisé par un véhicule
        ON UPDATE CASCADE  -- Si l'ID du type change, il se met à jour dans Vehicule
);


CREATE TABLE Agent (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    voie VARCHAR(50) NOT NULL,
    debut DATETIME NOT NULL,
    fin DATETIME NOT NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id),

    UNIQUE (user_id),

    CONSTRAINT fk_agent
        FOREIGN KEY (user_id)
        REFERENCES Users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)


CREATE TABLE Guichet (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    emplacement VARCHAR(150) NOT NULL,
    created_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    updated_at TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3) ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (id)
)


CREATE TABLE Agent_Guichet (
  agent_id INT UNSIGNED NOT NULL,
  guichet_id INT UNSIGNED NOT NULL,
  date_assignation TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  PRIMARY KEY (agent_id, guichet_id),

  CONSTRAINT fk_agent_guichet_agent
    FOREIGN KEY (agent_id)
    REFERENCES Agent(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT fk_agent_guichet_guichet
    FOREIGN KEY (guichet_id)
    REFERENCES Guichet(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)