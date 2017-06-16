<?php

/**
* Handle users related queries
*/

class Users extends BasicDB
{
  protected $table_name = "users" ;
  protected $user = NULL ;
  /**
  * Constructor function that initialize variable from BasicDB class
  */
  function __construct() {
    parent::__construct();
    $this->_setTable($this->table_name) ;
  }
  /**
  * Is user exist in our database if exist it will be authenticated
  * @return array of user object
  */
  public function authenticate($email, $password) {
    $sql = 'select * from ' . $this->_table;
		$sql .= ' where email = :email and password = :pass';

		$statement = $this->_dbh->prepare($sql);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':pass',  md5(md5($password)));
		$statement->execute();
		if($this->user  = $statement->fetch(PDO::FETCH_OBJ)){
      // auth okay, setup session WITHOUT the password
      unset($this->user->password) ;
      $_SESSION['user'] =  $this->user;
    } else {
      // didn't find that user
      return false ;
    }
  }
  /**
  * register a new user
  * @param $form Array
  * @param $form[name] string
  * @param $form[email] string
  * @param $form[password] string
  * @param $form[role] int
  */
  public function register($form) {
    // Very Basic & stupid Validation
    if(count($form) == 4 && !empty($form['name']) && !empty($form['email']) && !empty($form['password']) && !empty($form['role'])) {
    // Very basic MD5 encryption this should be replaced with salt based encryption with crypt() function
    $form['password'] = md5(md5($form['password'])) ;
    // Store it in the database
    $this->save($form) ;
    // Here we go...
    return true ;
    }else{
    return false ;
    }
  }
}
