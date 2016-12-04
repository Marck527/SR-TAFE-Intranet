###############################################
#Insert privileges
###############################################
DELIMITER //
CREATE PROCEDURE insertUserPrivilege(IN fldID BIGINT, IN fldPrivilegeTitle VARCHAR(20))
BEGIN
 INSERT INTO tblUserPrivilege(fldID, fldPrivilegeTitle)VALUES(fldID, fldPrivilegeTitle);
END //
DELIMITER ;

###############################################
#Insert document type
###############################################
DELIMITER //
CREATE PROCEDURE insertDocumentType(IN document_type_id VARCHAR(6), IN document_type_name VARCHAR(25))
BEGIN
	INSERT INTO tblDocumentType(fldID, fldDocumentTypeName)VALUES(document_type_id, document_type_name);
END //
DELIMITER ;

###############################################
#Insert WHS incident status
###############################################
DELIMITER //
CREATE PROCEDURE insertWHSIncidentStatus(IN whs_incident_id VARCHAR(6), IN whs_incident_status_name VARCHAR(20))
BEGIN
	INSERT INTO tblWHSIncidentStatus(fldID, fldIncidentStatusName)VALUES(whs_incident_id, whs_incident_status_name);
END //
DELIMITER ;

###############################################
#Insert WHS incident type
###############################################
DELIMITER //
CREATE PROCEDURE insertWHSIncidentType(IN whs_incident_type_id VARCHAR(6), IN whs_incident_type_name VARCHAR(50), IN whs_incident_type_description VARCHAR(400))
BEGIN
	INSERT INTO tblWHSIncidentType(fldID, fldIncidentTypeName, fldIncidentTypeDescription) VALUES(whs_incident_type_id, whs_incident_type_name, whs_incident_type_description);
END //
DELIMITER ;

###############################################
#Insert campus block
###############################################
DELIMITER //
CREATE PROCEDURE insertCampusBlock(IN campus_block_id VARCHAR(6), IN campus_block_name VARCHAR(10))
BEGIN
	INSERT INTO tblCampusBlock(fldID, fldCampusBlockName)VALUES(campus_block_id, campus_block_name);
END //
DELIMITER ;
###############################################
#Insert user
###############################################
DELIMITER //
CREATE PROCEDURE insertUser(IN first_name VARCHAR(30), IN last_name VARCHAR(30), IN email VARCHAR(50), IN user_name VARCHAR(30), IN pass_word VARCHAR(200), IN privilege TINYINT)
BEGIN
	INSERT INTO tblUser(fldFirstName, fldLastName, fldEmail, fldUsername, fldPassword, fldFKPrivilegeID)VALUES(first_name, last_name, email, user_name, pass_word, privilege);
    
END //
DELIMITER ;
###############################################
#Update user
###############################################
DELIMITER //
CREATE PROCEDURE updateUser(IN first_name VARCHAR(30), IN last_name VARCHAR(30), IN email VARCHAR(50), IN user_name VARCHAR(30), IN pass_word VARCHAR(200), IN privilege TINYINT, IN user_id BIGINT)
BEGIN
	UPDATE
		tblUser 
	SET
		fldFirstName     = first_name,
        fldLastName      = last_name,
        fldEmail         = email,
        fldUsername      = user_name,
        fldPassword      = pass_word,
        fldFKPrivilegeID = privilege
	WHERE
		fldID = user_id;
    
END //
DELIMITER ;
###############################################
#Delete user
###############################################
DELIMITER //
CREATE PROCEDURE deleteUser(IN user_id BIGINT)
BEGIN
	DELETE FROM
		tblUser
	WHERE
		fldID = user_id;
    
END //
DELIMITER ;
###############################################
#Search user id
###############################################
DELIMITER //
CREATE PROCEDURE searchUserID(IN user_id BIGINT)
BEGIN
	SELECT
		fldID,
        fldFirstName,
        fldLastName,
        fldEmail,
        fldUsername,
        fldPassword,
        fldFKPrivilegeID
	FROM
		tblUser
	WHERE
		fldID = user_id;
END //
DELIMITER ;
###############################################
#Search username
###############################################
DELIMITER //
CREATE PROCEDURE searchUsername(IN user_name VARCHAR(30))
BEGIN
	SELECT * FROM tblUser WHERE binary fldUsername = binary user_name;
END //
DELIMITER ;
###############################################
#Search email
###############################################
DELIMITER //
CREATE PROCEDURE searchEmail(IN email VARCHAR(50))
BEGIN
	SELECT * FROM tblUser WHERE fldEmail = email;
END //
DELIMITER ;
###############################################
#View policies
###############################################
DELIMITER //
CREATE PROCEDURE viewPolicies()
BEGIN
	SELECT
		fldID,
		fldDocumentName,
		fldDocumentLocation,
		fldFKDocumentTypeID
	FROM
		tblDocument
	WHERE
    fldFKDocumentTypeID = 'pol';
END //
DELIMITER ;
###############################################
#Document search by name
###############################################
DELIMITER //
CREATE PROCEDURE documentSearch(IN keywordSearch VARCHAR(50))
BEGIN
	SELECT
		D.fldID AS fldDOCID,
        D.fldDocumentName,
        D.fldDocumentLocation,
        DT.fldDocumentTypeName
	
	FROM
		tblDocument D
	INNER JOIN
		tblDocumentType DT
	ON
		D.fldFKDocumentTypeID = DT.fldID
	LEFT JOIN
		tblSustainabilityProjectDocument SPD
	ON
		D.fldID = SPD.fldFKSustainabilityProjectDocumentID
	LEFT JOIN
		tblSustainabilityProject SP
	ON
		fldFKSustainabilityProjectID = SP.fldID
	LEFT JOIN
		tblWHSIncidentDocument WID
	ON
		D.fldID = WID.fldFKWHSIncidentDocumentID
	LEFT JOIN
		tblWHSIncident WI
	ON
		WID.fldFKWHSIncidentID = WI.fldID
	WHERE
		D.fldDocumentName LIKE CONCAT ('%', keywordSearch, '%');
END //
DELIMITER ;
###############################################
#Document search by id
###############################################
DELIMITER //
CREATE PROCEDURE documentSearchID(IN documentID BIGINT)
BEGIN
	SELECT
		D.fldID AS fldDOCID,
        D.fldDocumentName,
        D.fldDocumentLocation,
        D.fldFKDocumentTypeID,
        DT.fldDocumentTypeName,
        SP.fldSustainabilityProjectDate,
        SP.fldSustainabilityProjectDescription,
        WI.fldIncidentDate,
        WIT.fldIncidentTypeName,
        CBS.fldID AS fldCampusBlockProjectID,
        CBS.fldCampusBlockName AS fldCampusProjectBlock, 
        CBR.fldID AS fldCampusBlockReportID,
        CBR.fldCampusBlockName AS fldCampusIncidentBlock,
        WI.fldIncidentDate,
        WI.fldIncidentDateRemind,
        WI.fldFKIncidentTypeID
	FROM
		tblDocument D
	INNER JOIN
		tblDocumentType DT
	ON
		D.fldFKDocumentTypeID = DT.fldID
	LEFT JOIN
		tblSustainabilityProjectDocument SPD
	ON
		D.fldID = SPD.fldFKSustainabilityProjectDocumentID
	LEFT JOIN
		tblSustainabilityProject SP
	ON
		fldFKSustainabilityProjectID = SP.fldID
	LEFT JOIN
		tblWHSIncidentDocument WID
	ON
		D.fldID = WID.fldFKWHSIncidentDocumentID
	LEFT JOIN
		tblWHSIncident WI
	ON
		WID.fldFKWHSIncidentID = WI.fldID
	LEFT JOIN
		tblWHSIncidentType WIT
	ON
		WI.fldFKIncidentTypeID = WIT.fldID
	LEFT JOIN
		tblCampusBlock CBS
	ON
		SP.fldFKProjectBlockID = CBS.fldID
	LEFT JOIN
		tblCampusBlock CBR
	ON
		WI.fldFKIncidentBlockID = CBR.fldID
	WHERE
		D.fldID = documentID;
END //
DELIMITER ;
###############################################
#Delete document
###############################################
DELIMITER //
CREATE PROCEDURE deleteDocument(IN document_id BIGINT)
BEGIN
	DELETE FROM
		tblDocument
	WHERE
		fldID = document_id;
END //
DELIMITER ;
###############################################
#Insert staff
###############################################
DELIMITER //
CREATE PROCEDURE insertStaff(IN first_name VARCHAR(30), IN last_name VARCHAR(30), IN biography TEXT, IN photo VARCHAR(200))
BEGIN
	INSERT INTO tblStaff(fldFirstName, fldLastName, fldBiography, fldPhoto)VALUES(first_name, last_name, biography, photo);
END //
DELIMITER ;
###############################################
#Update staff
###############################################
DELIMITER //
CREATE PROCEDURE updateStaff(IN first_name VARCHAR(30), IN last_name VARCHAR(30), IN biography TEXT, IN photo VARCHAR(200), IN staff_id BIGINT)
BEGIN
	UPDATE
		tblStaff
	SET
		fldFirstName = first_name, fldLastName = last_name, fldBiography = biography, fldPhoto = photo
	WHERE
		fldID = staff_id;
END //
DELIMITER ;
###############################################
#View staff
###############################################
DELIMITER //
CREATE PROCEDURE viewStaff()
BEGIN
	SELECT
		fldID,
		CONCAT(fldFirstName, ' ', fldLastName) AS fldStaffName,
        fldBiography,
        fldPhoto
	FROM
		tblStaff;
END //
DELIMITER ;


#REPORT QUERIES

###############################################
#Incident by type
###############################################
DELIMITER //
CREATE PROCEDURE incidentByType()
BEGIN
	SELECT
	COUNT(WI.fldFKIncidentTypeID) AS fldCategoryCount, WIT.fldIncidentTypeName
FROM
	tblWHSIncident WI
INNER JOIN
	tblWHSIncidentType WIT
ON
	WI.fldFKIncidentTypeID = WIT.fldID
GROUP BY
	WI.fldFKIncidentTypeID;
END //
DELIMITER ;
###############################################
#Incident by block
###############################################
DELIMITER //
CREATE PROCEDURE incidentByBlock()
BEGIN
	SELECT
	COUNT(WI.fldFKIncidentBlockID) AS fldBlockCount, CB.fldCampusBlockName
FROM
	tblWHSIncident WI
INNER JOIN
	tblCampusBlock CB
ON
	WI.fldFKIncidentBlockID = CB.fldID
GROUP BY
	WI.fldFKIncidentBlockID;
END //
DELIMITER ;
###############################################
#Count incidents with reminders
###############################################
DELIMITER //
CREATE PROCEDURE countIncidentReminders()
BEGIN
	SELECT
		COUNT(fldID) AS fldIncidentReminderCount
	FROM
		tblWHSIncident
	WHERE
		fldIncidentDateRemind IS NOT NULL;
END //
DELIMITER ;

###############################################
#All incident reminders
###############################################
DELIMITER //
CREATE PROCEDURE allIncidentReminders()
BEGIN
	SELECT
		WI.fldIncidentName,
		WI.fldIncidentDate,
		WI.fldFKIncidentBlockID,
		WI.fldFKIncidentTypeID,
		WI.fldIncidentDateRemind,
		WIT.fldIncidentTypeName,
		WIT.fldIncidentTypeDescription,
		CB.fldCampusBlockName,
		WIT.fldIncidentTypeDescription,
		D.fldID AS fldDocumentID,
		D.fldDocumentLocation
	FROM
	  tblWHSIncident WI
	INNER JOIN
	  tblWHSIncidentType WIT
	ON 
	  WI.fldFKIncidentTypeID = WIT.fldID
	INNER JOIN
	  tblCampusBlock CB
	ON 
	  WI.fldFKIncidentBlockID = CB.fldID
	INNER JOIN
	  tblWHSIncidentDocument WID
	ON
	  WI.fldID = fldFKWHSIncidentID
	INNER JOIN
	  tblDocument D
	ON
	  WID.fldFKWHSIncidentDocumentID = D.fldID
	WHERE
	  WI.fldIncidentDateRemind IS NOT NULL
	ORDER BY
	  WI.fldIncidentDateRemind ASC;
END //
DELIMITER ;
###############################################
#Due this week incident reminders
###############################################
DELIMITER //
CREATE PROCEDURE dueThisWeekIncidentReminders()
BEGIN
	SELECT
		WI.fldIncidentName,
		WI.fldIncidentDate,
		WI.fldFKIncidentBlockID,
		WI.fldFKIncidentTypeID,
		WI.fldIncidentDateRemind,
		WIT.fldIncidentTypeName,
		WIT.fldIncidentTypeDescription,
		CB.fldCampusBlockName,
		WIT.fldIncidentTypeDescription,
		D.fldID AS fldDocumentID,
		D.fldDocumentLocation
	FROM
	  tblWHSIncident WI
	INNER JOIN
	  tblWHSIncidentType WIT
	ON 
	  WI.fldFKIncidentTypeID = WIT.fldID
	INNER JOIN
	  tblCampusBlock CB
	ON 
	  WI.fldFKIncidentBlockID = CB.fldID
	INNER JOIN
	  tblWHSIncidentDocument WID
	ON
	  WI.fldID = fldFKWHSIncidentID
	INNER JOIN
	  tblDocument D
	ON
	  WID.fldFKWHSIncidentDocumentID = D.fldID
	WHERE
	  WI.fldIncidentDateRemind IS NOT NULL
	AND
		 WI.fldIncidentDateRemind BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
	ORDER BY
	  WI.fldIncidentDateRemind ASC;
END //
DELIMITER ;
###############################################
#Overdue reminders
###############################################
DELIMITER //
CREATE PROCEDURE overdueIncidentReminders()
BEGIN
	SELECT
		WI.fldIncidentName,
		WI.fldIncidentDate,
		WI.fldFKIncidentBlockID,
		WI.fldFKIncidentTypeID,
		WI.fldIncidentDateRemind,
		WIT.fldIncidentTypeName,
		WIT.fldIncidentTypeDescription,
		CB.fldCampusBlockName,
		WIT.fldIncidentTypeDescription,
		D.fldID AS fldDocumentID,
		D.fldDocumentLocation
	FROM
	  tblWHSIncident WI
	INNER JOIN
	  tblWHSIncidentType WIT
	ON 
	  WI.fldFKIncidentTypeID = WIT.fldID
	INNER JOIN
	  tblCampusBlock CB
	ON 
	  WI.fldFKIncidentBlockID = CB.fldID
	INNER JOIN
	  tblWHSIncidentDocument WID
	ON
	  WI.fldID = fldFKWHSIncidentID
	INNER JOIN
	  tblDocument D
	ON
	  WID.fldFKWHSIncidentDocumentID = D.fldID
	WHERE
	  CURDATE() > WI.fldIncidentDateRemind
	ORDER BY
	  WI.fldIncidentDateRemind ASC;
END //
DELIMITER ;

###############################################
#Delete sustainability project
###############################################
DELIMITER //
CREATE PROCEDURE deleteSusProject(IN project_id BIGINT)
BEGIN
	DELETE FROM
		tblSustainabilityProject 
	WHERE
		fldID = project_id;
END //
DELIMITER ;
###############################################
#Delete whs incident
###############################################
DELIMITER //
CREATE PROCEDURE deleteWHSIncident(IN incident_id BIGINT)
BEGIN
	DELETE FROM
		tblWHSIncident 
	WHERE
		fldID = incident_id;
END //
DELIMITER ;

###############################################
#Master join
###############################################
DELIMITER //
CREATE PROCEDURE masterJoin(IN document_id BIGINT)
BEGIN
	SELECT 
	D.fldID AS fldDocumentID, D.fldDocumentName, D.fldDocumentLocation, D.fldFKDocumentTypeID,
	SP.fldID AS fldSustainabilityProjectID, SP.fldSustainabilityProjectName,
	WI.fldID AS fldWHSIncidentID, WI.fldIncidentName
	FROM
	tblDocument D
	LEFT JOIN tblDocumentType DT ON D.fldFKDocumentTypeID = DT.fldID
	#Left joins the user and user privilege table
	LEFT JOIN tblUser U ON D.fldFKUserID = U.fldID
	LEFT JOIN tblUserPrivilege UP ON U.fldFKPrivilegeID = UP.fldID
	#Left joins the sustainability tables as well as the campus block table (with a different nickname than the whs campus block table)
	LEFT JOIN tblSustainabilityProjectDocument SPD ON D.fldID = SPD.fldFKSustainabilityProjectDocumentID
	LEFT JOIN tblSustainabilityProject SP ON SPD.fldFKSustainabilityProjectID = SP.fldID
	LEFT JOIN tblCampusBlock CBS ON SP.fldFKProjectBlockID = CBS.fldID
	#Left joins the whs tables. Campus block table has a different nickname to differentiate
	LEFT JOIN tblWHSIncidentDocument WID ON D.fldID = WID.fldFKWHSIncidentDocumentID
	LEFT JOIN tblWHSIncident WI ON WID.fldFKWHSIncidentID = WI.fldID
	LEFT JOIN tblCampusBlock CBI ON WI.fldFKIncidentBlockID = CBI.fldID
    
    WHERE D.fldID = document_id;
END //
DELIMITER ;


###############################################
#Count Admins
###############################################
DELIMITER //
CREATE PROCEDURE countAdmins()
BEGIN
	SELECT COUNT(fldFKPrivilegeID) AS fldAdminCount
	FROM tblUser
	WHERE
	fldFKPrivilegeID = 1;
END //
DELIMITER ;












