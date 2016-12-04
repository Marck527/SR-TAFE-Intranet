###############################################
#Inserts user privileges
###############################################
CALL insertUserPrivilege(1, 'Admin');
CALL insertUserPrivilege(2, 'Editor');
CALL insertUserPrivilege(3, 'Viewer');

###############################################
#Inserts document types
###############################################
CALL insertDocumentType('pol', 'Policy');
CALL insertDocumentType('rep', 'WHS Report');
CALL insertDocumentType('proj', 'Sustainability Project');

###############################################
#Inserts whs incident type
###############################################
CALL insertWHSIncidentType('haz', 'Hazard', 'A hazard');
CALL insertWHSIncidentType('near', 'Near Miss', 'A near miss incident');
CALL insertWHSIncidentType('inc', 'Incident', 'A whs incident');


###############################################
#Inserts campus blocks
###############################################
CALL insertCampusBlock('d', 'D-Block');
CALL insertCampusBlock('c', 'C-Block');
CALL insertCampusBlock('g', 'G-Block');
CALL insertCampusBlock('h', 'H-Block');
CALL insertCampusBlock('j', 'J-Block');
CALL insertCampusBlock('k', 'K-Block');
CALL insertCampusBlock('p', 'P-Block');
CALL insertCampusBlock('o', 'O-Block');
CALL insertCampusBlock('f', 'F-Block');
CALL insertCampusBlock('m', 'M-Block');
CALL insertCampusBlock('a', 'A-Block');
CALL insertCampusBlock('b', 'B-Block');
CALL insertCampusBlock('l', 'L-Block');
CALL insertCampusBlock('n', 'N-Block');
CALL insertCampusBlock('r', 'R-Block');
CALL insertCampusBlock('s', 'S-Block');

###############################################
#Inserts users
###############################################
CALL insertUser('Marck', 'Munoz', 'Marck527@gmail.com', 'Marck527', '$2y$10$qrk9RjomlU2BhBZcjnWLbuDzU3WctCNJ8pMoJBiMkAI2QhhlpnqCu', 1);
CALL insertUser('Brayden', 'Gravestock', 'Brayden.Gravestock@gmail.com', 'Brayden', '$2y$10$qrk9RjomlU2BhBZcjnWLbuDzU3WctCNJ8pMoJBiMkAI2QhhlpnqCu', 2);
CALL insertUser('Caleb', 'Olver', 'Caleb.Olver@gmail.com', 'Caleb', '$2y$10$qrk9RjomlU2BhBZcjnWLbuDzU3WctCNJ8pMoJBiMkAI2QhhlpnqCu', 3);
