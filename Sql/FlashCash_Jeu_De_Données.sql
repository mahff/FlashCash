ALTER SEQUENCE admin_idadmin_seq RESTART WITH 1;
ALTER SEQUENCE administrationclient_idoperation_seq RESTART WITH 1;
ALTER SEQUENCE administrationmachine_idoperation_seq RESTART WITH 1;
ALTER SEQUENCE depot_iddepot_seq RESTART WITH 1;
ALTER SEQUENCE machine_idmachine_seq RESTART WITH 1;
ALTER SEQUENCE qr_idqr_seq RESTART WITH 1;
ALTER SEQUENCE utilisateur_idutilisateur_seq RESTART WITH 1;


INSERT INTO utilisateur(mail, login, telephone, hashmotdepasse,etat, solde)

			VALUES('vatef@test.fr','Vatef', '101010101','7c4a8d09ca3762af61e59520943dc26494f8941b', 'Maintenu', '0'),
				  ('crist@service.fr','Crist5252', '102020202','7c4a8d09ca3762af61e59520943dc26494f8941b','Maintenu', '0'),
				  ('bouffe@phonix.fr','bouffeurPhonix', '103030303','7c4a8d09ca3762af61e59520943dc26494f8941b','Maintenu', '0'),
				  ('bob@outlook.fr','BobLeBG', '104050607','7c4a8d09ca3762af61e59520943dc26494f8941b','Maintenu', '0'),
				  ('mah@service.net','Mahmoud', '108091011','7c4a8d09ca3762af61e59520943dc26494f8941b','Maintenu', '0');

INSERT INTO professionnel(idutilisateur, nompro, siret)
						VALUES('1','Vatef','5181684329850'),
				  			  ('3','phonix','0349203910392');

INSERT INTO particulier (idutilisateur, nompar, prenom)
						VALUES ('2', 'Crist', 'Lemarc'),
							    ('4','Bob', 'Guildart'),
							   ('5','Mahmoud', 'Bellala');


INSERT INTO qR (destinataire, montant, produit, dateexpiration) /*destinataire ce n'est pas utilisateur ici?? du coup idutilisateur*/
			   VALUES('3', '0.5', 'Banane', '2018-10-15 14:10:25'),
					 ('3', '1.20', 'Coca', '2018-10-16 14:50:25'),
					 ('2', '3.25', 'Repas RU', '2018-10-19 12:16:25'),
					 ('1', '0.5', 'Service', '2018-10-31 15:10:25');

INSERT INTO transaction (idqr,idutilisateur,date, service)
						VALUES('1','1','2018-10-15 15:10:33.000000','Banane'),
							  ('2','5','2018-10-16 16:30:50.000000','Coca'),
							  ('3','2','2018-10-19 12:32:50.000000','Repas RU'),
							  ('4','3','2018-10-16 10:04:20.000000','Service');

INSERT INTO admin (nomadm, prenomadm, hashmotdepasse)
				  VALUES('Lemaire', 'marc', '8eb59d836774f3a2c0e77378e3401bcc96f54bf3'),
						('Tao', 'Jen', '8eb59d836774f3a2c0e77378e3401bcc96f54bf3'),
						('Tram', 'Tuyet', '8eb59d836774f3a2c0e77378e3401bcc96f54bf3');

INSERT INTO machine (lieu,datemiseservice,stock,statut)
					VALUES('Rotande STM', '2018-10-10', '120.1', 'En service'),
						  ('Chênes', '2018-10-12', '130.5', 'En service'),
						  ('Neuville', '2018-10-14', '15.6', 'Maintenance');

INSERT INTO administrationMachine (idadmin,idmachine,operationmachine) VALUES('1','1', 'Depot'),
										('2','3', 'Retrait'),
										('3','2', 'Maintenance');

INSERT INTO administrationClient (idadmin,idutilisateur,operationclient,observation)
								 VALUES('1', '1', 'Suspendu', NULL),
									   ('3', '2', NULL, NULL),
									   ('2', '3', 'Bloque', 'Tentative escroquerie');

INSERT INTO depot (idutilisateur, idmachine, date, montant)
			 VALUES('3','2','2018-10-20','6.33');
