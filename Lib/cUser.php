<?php

/*
 * Author: Marck Munoz
 * Date: 27/07/2016
 * Comments: Trying out OOP in PHP.
 */

class cUser {
    private $_oConn;
    private $_errors = [];

    function __construct($i_oConn) {
        $this->_oConn = $i_oConn;
    }
    public function register($i_fname, $i_lname, $i_email_addr, $i_chosen_uname, $i_chosen_pword, $i_privilege) {
        $generated_hash = password_hash($i_chosen_pword, PASSWORD_DEFAULT, ['cost' => 10]);

        if($this->checkUsername($i_chosen_uname)) {

            $this->_errors[] = "The username {$i_chosen_uname} has already been taken.";

        }
        if($this->checkEmail($i_email_addr)) {

            $this->_errors[] = "The email {$i_email_addr} is already being used.";

        }

        if (empty($this->_errors)) {
            $sSQL = <<<SQL
            CALL insertUser(:first_name, :last_name, :email, :username, :password, :privilege_id);
SQL;
            $oStmt = $this->_oConn->prepare($sSQL);
            $oStmt->execute(
                [
                    'first_name'   => $i_fname,
                    'last_name'    => $i_lname,
                    'email'        => $i_email_addr,
                    'username'     => $i_chosen_uname,
                    'password'     => $generated_hash,
                    'privilege_id' => $i_privilege
                ]
            );

            return true;
        }

        return false;
    }
    public function update($i_fname, $i_lname, $i_email_addr, $i_chosen_uname, $i_chosen_pword, $i_privilege, $i_user_id) {
        $generated_hash = password_hash($i_chosen_pword, PASSWORD_DEFAULT, ['cost' => 10]);

        if (empty($this->_errors)) {
            $sSQL = <<<SQL
            
            CALL updateUser(:first_name, :last_name, :email, :username, :password, :privilege_id, :user_id);
SQL;
            $oStmt = $this->_oConn->prepare($sSQL);
            $oStmt->execute(
                [
                    'first_name'   => $i_fname,
                    'last_name'    => $i_lname,
                    'email'        => $i_email_addr,
                    'username'     => $i_chosen_uname,
                    'password'     => $generated_hash,
                    'privilege_id' => $i_privilege,
                    'user_id'      => $i_user_id
                ]
            );

            return true;
        }

        return false;
    }
    public function update2($i_fname, $i_lname, $i_email_addr, $i_chosen_uname, $i_privilege, $i_user_id) {

        if (empty($this->_errors)) {
            $sSQL = <<<SQL
            
            UPDATE
              tblUser
            SET 
              fldFirstName=:first_name, fldLastName=:last_name, fldEmail=:email, fldUsername=:username, fldFKPrivilegeID=:privilege_id
            WHERE
              fldID = :user_id;
SQL;
            $oStmt = $this->_oConn->prepare($sSQL);
            $oStmt->execute(
                [
                    'first_name'   => $i_fname,
                    'last_name'    => $i_lname,
                    'email'        => $i_email_addr,
                    'username'     => $i_chosen_uname,
                    'privilege_id' => $i_privilege,
                    'user_id'      => $i_user_id
                ]
            );

            return true;
        }

        return false;
    }
    public function deleteUser($i_user_id) {
        $sSQL =<<<SQL
        CALL deleteUser(:user_id);
SQL;
        $oStmt = $this->_oConn->prepare($sSQL);
        $oStmt->execute(
            [
                'user_id' => $i_user_id
            ]
        );

    }
    private function checkUsername($i_username) {
        $sSQL = <<<SQL
        CALL searchUsername(:username);
  		
SQL;
  		$oStmt = $this->_oConn->prepare($sSQL);
  		$oStmt->execute([
  		    'username' => $i_username
        ]);

  		if($oStmt->rowCount()) {
  			return true;
  		}
  		return false;
    }
   private function checkEmail($i_email) {
        $sSQL = <<<SQL
        
        CALL searchEmail(:email);
        
SQL;
        $oStmt = $this->_oConn->prepare($sSQL);
        $oStmt->execute([
            'email' => $i_email
        ]);

        if($oStmt->rowCount()) {
            return true;
        }
        return false;
   }
  public function login($i_username, $i_password) {
      $sSQL = <<<SQL
			CALL searchUsername(:username);

SQL;
      $oStmt = $this->_oConn->prepare($sSQL);
      $oStmt->execute([
          'username' => $i_username
      ]);

      $user_row = $oStmt->fetch(PDO::FETCH_OBJ);

      if($oStmt->rowCount()) {
          if(password_verify($i_password, $user_row->fldPassword)) {

              $_SESSION['user_id'] = $user_row->fldID;
              $_SESSION['user_logged'] = $user_row->fldUsername;
              $_SESSION['user_permission'] = $user_row->fldFKPrivilegeID;

              return true;
          } else {
              $this->_errors[] = "Incorrect username or password";
          }
      } else {
          $this->_errors[] = "Incorrect username or password";
      }
      return false;
  }
  public static function logout() {
      unset($_SESSION['user_id']);
      session_destroy();
      header('location: Main.php');
  }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

}
