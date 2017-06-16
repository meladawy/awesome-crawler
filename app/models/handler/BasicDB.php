<?php

/**
* Basic database connection
*/
class BasicDB
{
  protected $_dbh = null;
	protected $_table = "";

  /**
  * Constructor function
  * Connect to the database
  */
  function __construct ()
  {
    $settings = parse_ini_file(BASE_PATH . '/config/settings.ini', true);
    // starts the connection to the database
    $this->_dbh = new PDO(
    	sprintf(
    		"%s:host=%s;dbname=%s",
    		$settings['driver'],
    		$settings['host'],
    		$settings['dbname']
    	),
    	$settings['user'],
    	$settings['password'],
    	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    
  }

  /**
	 * Sets the database table the model is using
	 * @param string $table the table the model is using
	 */
	protected function _setTable($table)
	{
		$this->_table = $table;
	}

  /**
  * retreive value of record
  * @param int $id the id of of the record to retrieve
  */
	public function fetchOne($id)
	{
		$sql = 'select * from ' . $this->_table;
		$sql .= ' where id = ?';

		$statement = $this->_dbh->prepare($sql);
		$statement->execute(array($id));

		return $statement->fetch(PDO::FETCH_OBJ);
	}

	/**
	 * Saves the current data to the database. If an key named "id" is given,
	 * an update will be issued.
	 * @param array $data the data to save
	 * @return int the id the data was saved under
	 */
	public function save($data = array())
	{
		$sql = '';

		$values = array();

		if (array_key_exists('id', $data)) {
			$sql = 'update ' . $this->_table . ' set ';

			$first = true;
			foreach($data as $key => $value) {
				if ($key != 'id') {
					$sql .= ($first == false ? ',' : '') . ' ' . $key . ' = ?';

					$values[] = $value;

					$first = false;
				}
			}

			// adds the id as well
			$values[] = $data['id'];

			$sql .= ' where id = ?';// . $data['id'];

			$statement = $this->_dbh->prepare($sql);
			return $statement->execute($values);
		}
		else {
			$keys = array_keys($data);

			$sql = 'insert into ' . $this->_table . '(';
			$sql .= implode(',', $keys);
			$sql .= ')';
			$sql .= ' values (';

			$dataValues = array_values($data);
			$first = true;
			foreach($dataValues as $value) {
				$sql .= ($first == false ? ',?' : '?');

				$values[] = $value;

				$first = false;
			}

			$sql .= ')';

			$statement = $this->_dbh->prepare($sql);
			if ($statement->execute($values)) {
				return $this->_dbh->lastInsertId();
			}
		}

		return false;
	}

	/**
	 * Deletes a single entry
	 * @param int $id the id of the entry to delete
	 * @return boolean true if all went well, else false.
	 */
	public function delete($id)
  {
		$statement = $this->_dbh->prepare("delete from " . $this->_table . " where id = ?");
		return $statement->execute(array($id));
  }
  /**
  * @param string $variable that will be filtered from sql injection
  */
  function make_safe($variable)
  {
     $variable = mysql_real_escape_string(trim($variable));
     return $variable;
  }
}