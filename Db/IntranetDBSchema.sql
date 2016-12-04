DROP DATABASE IF EXISTS IntranetDBSchema;
CREATE DATABASE IntranetDBSchema;
USE IntranetDBSchema;

CREATE TABLE tblStaff
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
	fldFirstName VARCHAR(30) NOT NULL,
    fldLastName VARCHAR(30) NOT NULL,
    fldBiography TEXT NOT NULL,
    fldPhoto VARCHAR(200) NOT NULL
);

CREATE TABLE tblUserPrivilege
(
	fldID TINYINT PRIMARY KEY NOT NULL,
    fldPrivilegeTitle VARCHAR(20) NOT NULL
);

CREATE TABLE tblUser
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fldFirstName VARCHAR(30) NOT NULL,
    fldLastName VARCHAR(30) NOT NULL,
    fldEmail VARCHAR(50),
    fldUsername VARCHAR(30) NOT NULL,
    fldPassword VARCHAR(200) NOT NULL,
    
    fldFKPrivilegeID TINYINT NOT NULL,
    
    FOREIGN KEY(fldFKPrivilegeID) REFERENCES tblUserPrivilege(fldID) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE tblDocumentType
(
	fldID VARCHAR(6) PRIMARY KEY NOT NULL,
    fldDocumentTypeName VARCHAR(25) NOT NULL
);

CREATE TABLE tblDocument
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fldDocumentName VARCHAR(40),
    fldDocumentLocation VARCHAR(200),
    
    fldFKDocumentTypeID VARCHAR(6) NOT NULL,
    fldFKUserID BIGINT NOT NULL,
    
    FOREIGN KEY(fldFKDocumentTypeID) REFERENCES tblDocumentType(fldID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY(fldFKUserID) REFERENCES tblUser(fldID) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tblCampusBlock
(
	fldID VARCHAR(6) PRIMARY KEY NOT NULL,
    fldCampusBlockName VARCHAR(10) NOT NULL
);

CREATE TABLE tblWHSIncidentType
(
	fldID VARCHAR(6) PRIMARY KEY NOT NULL,
    fldIncidentTypeName VARCHAR(50) NOT NULL,
    fldIncidentTypeDescription VARCHAR(400) NOT NULL
);


CREATE TABLE tblWHSIncident
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fldIncidentName VARCHAR(40) NOT NULL,
    fldIncidentDate DATE NOT NULL,
    fldIncidentDateRemind DATE,
    
    fldFKIncidentTypeID VARCHAR(6) NOT NULL,
    fldFKIncidentBlockID VARCHAR(6) NOT NULL,
    
    FOREIGN KEY(fldFKIncidentTypeID) REFERENCES tblWHSIncidentType(fldID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(fldFKIncidentBlockID) REFERENCES tblCampusBlock(fldID) ON DELETE RESTRICT ON UPDATE CASCADE
    
);

CREATE TABLE tblWHSIncidentDocument
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    
    fldFKWHSIncidentDocumentID BIGINT NOT NULL,
    fldFKWHSIncidentID BIGINT NOT NULL,
    
    FOREIGN KEY(fldFKWHSIncidentDocumentID) REFERENCES tblDocument(fldID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(fldFKWHSIncidentID) REFERENCES tblWHSIncident(fldID) ON DELETE CASCADE ON UPDATE CASCADE
    
);

CREATE TABLE tblSustainabilityProject
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    fldSustainabilityProjectName VARCHAR(40) NOT NULL,
    fldSustainabilityProjectDate DATE NOT NULL,
    fldSustainabilityProjectDescription TEXT NOT NULL,
    
    fldFKProjectBlockID VARCHAR(6) NOT NULL,
    
    FOREIGN KEY(fldFKProjectBlockID) REFERENCES tblCampusBlock(fldID) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE tblSustainabilityProjectDocument
(
	fldID BIGINT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    
    fldFKSustainabilityProjectDocumentID BIGINT NOT NULL,
    fldFKSustainabilityProjectID BIGINT NOT NULL,
    
    FOREIGN KEY(fldFKSustainabilityProjectDocumentID) REFERENCES tblDocument(fldID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY(fldFKSustainabilityProjectID) REFERENCES tblSustainabilityProject(fldID) ON DELETE CASCADE ON UPDATE CASCADE
);
