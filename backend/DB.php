<?php

class DB
{
    // Singleton instance of the DB class
    private static ?DB $instance = null;

    // PDO instance for database connection
    private ?PDO $_pdo = null;

    // Array to store query results
    private ?array $_results = null;

    // Private constructor to prevent direct object creation
	private function __construct()
    {
        // Database configuration
        $db_config = [
            'host'  => 'localhost',
            'user'  => 'root',
            'pass'  => '',
            'db'    => 'actionstep'
        ];

        // Attempt to create a PDO instance
		try {
			$this->_pdo = new PDO('mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['db'], $db_config['user'], $db_config['pass']);
		} catch(PDOExeption $e) {
			die($e->getMessage());
		}
	}

    /**
     * Get the singleton instance of the DB class
     * @return DB The singleton instance
     */
	static function getInstance(): DB
    {
		if(!isset(self::$instance)) {
			self::$instance = new DB();
		}

		return self::$instance;
	}

    /**
     * Execute a SQL query with optional parameters
     * @param string $sql The SQL query
     * @param array $params Optional parameters for the query
     * @return self The current instance
     */
	function query(string $sql, array $params = []): self
    {
        // Prepare the SQL statement
        if($query = $this->_pdo->prepare($sql)) {
			$x = 1;
            // Bind parameters to the query
			if(\count($params)) {
				foreach($params as $param) {
					$query->bindValue($x, $param);
					$x++;
				}	
			}

            // Execute the query
			if($query->execute()) {
				$this->_results = $query->fetchAll(PDO::FETCH_OBJ);
			}
		}

		return $this;
	}

    /**
     * Get all results from the last executed query
     * @return array|null The query results or null if no results
     */
	function results(): ?array
    {
		return $this->_results;
	}

    /**
     * Get the first result from the last executed query
     * @return object|null The first result or null if no results
     */
	function first(): ?object
    {
        return !empty($this->_results) ? $this->_results[0] : null;
	}
}
