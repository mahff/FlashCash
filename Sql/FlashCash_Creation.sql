DROP TABLE qr, utilisateur, particulier, professionnel, machine, transaction, admin, administrationClient, administrationMachine, depot CASCADE;
DROP TYPE state, status, operationC, operationM CASCADE;



CREATE TABLE qR(
	iDQR SERIAL NOT NULL,
	destinataire INTEGER NOT NULL,
	montant FLOAT NOT NULL,
	PRODUIT VARCHAR(100),
	dateExpiration TIMESTAMP,
	CONSTRAINT qr_pk PRIMARY KEY (iDQR)
);

CREATE TYPE state AS ENUM ('Maintenu','Suspendu','Bloque');

CREATE TABLE utilisateur(
	idUtilisateur SERIAL NOT NULL,
	mail VARCHAR(100) NOT NULL,
	login VARCHAR(50) NOT NULL,
	telephone VARCHAR(10) NOT NULL,
	hashMotDePasse VARCHAR(256) NOT NULL,
	etat state,
	solde FLOAT NOT NULL,
	CONSTRAINT utilisateur_pk PRIMARY KEY (idUtilisateur)
);

CREATE TABLE particulier(
	idUtilisateur INTEGER NOT NULL,
	nomPar VARCHAR(50) NOT NULL,
	prenom VARCHAR(50) NOT NULL,
	CONSTRAINT particulier_pk PRIMARY KEY (idUtilisateur)
);

CREATE TABLE professionnel(
	idUtilisateur INTEGER NOT NULL,
	nomPro VARCHAR(50) NOT NULL,
	siret VARCHAR(14) NOT NULL,
	CONSTRAINT professionnel_pk PRIMARY KEY (idUtilisateur)
);

CREATE TABLE transaction(
	iDQR INTEGER NOT NULL,
	idUtilisateur INTEGER NOT NULL,
	date TIMESTAMP NOT NULL,
	service VARCHAR(100),
	CONSTRAINT transaction_fk1 FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur),
	CONSTRAINT transaction_pk PRIMARY KEY (iDQR, idUtilisateur)
);

CREATE TABLE admin(
	idAdmin SERIAL NOT NULL,
	nomAdm VARCHAR(50) NOT NULL,
	prenomAdm VARCHAR(50) NOT NULL,
	hashMotDePasse VARCHAR(256) NOT NULL,
	CONSTRAINT admin_pk PRIMARY KEY (idAdmin)
);


CREATE TYPE status AS ENUM ('En service','Desactive', 'Maintenance');

CREATE TABLE machine(
	idMachine SERIAL NOT NULL,
	lieu VARCHAR(200) NOT NULL,
	dateMiseService DATE,
	stock FLOAT NOT NULL,
	statut status,
	CONSTRAINT machine_pk PRIMARY KEY(idMachine)
);


CREATE TYPE operationM AS ENUM ('Depot','Retrait','Maintenance');

CREATE TABLE administrationMachine(
	idOperation SERIAL NOT NULL,
	idAdmin INTEGER NOT NULL,
	idMachine INTEGER NOT NULL,
	operationMachine operationM NOT NULL,
	CONSTRAINT administrationMachine_fk1 FOREIGN KEY (idAdmin) REFERENCES admin(idAdmin),
	CONSTRAINT administrationMachine_fk2 FOREIGN KEY (idMachine) REFERENCES machine(idMachine),
	CONSTRAINT administrationMachine_pk PRIMARY KEY (idOperation)
);


CREATE TYPE operationC AS ENUM ('Suspendu','Bloque','Maintenu');

CREATE TABLE administrationClient(
	idOperation SERIAL NOT NULL,
	idAdmin INTEGER NOT NULL,
	idUtilisateur INTEGER NOT NULL,
	operationClient operationC,
	observation VARCHAR(256),
	CONSTRAINT administrationClient_fk1 FOREIGN KEY (idAdmin) REFERENCES admin(idAdmin),
	CONSTRAINT administrationClient_fk2 FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur),
	CONSTRAINT administrationClient_pk PRIMARY KEY (idOperation)
);

CREATE TABLE depot(
	idDepot SERIAL NOT NULL,
	idUtilisateur INTEGER NOT NULL,
	idMachine INTEGER NOT NULL,
	date DATE NOT NULL,
	montant FLOAT NOT NULL,
	CONSTRAINT depot_fk1 FOREIGN KEY (idUtilisateur) REFERENCES utilisateur(idUtilisateur),
	CONSTRAINT depot_fk2 FOREIGN KEY (idMachine) REFERENCES machine(idMachine),
	CONSTRAINT depot_pk PRIMARY KEY (idDepot)
);
