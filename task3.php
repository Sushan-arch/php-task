<?php

/**
 * Class TableCreator
 *
 * This class creates a table 'Test' with specific fields and provides methods to fill the table with random data
 * and retrieve data based on specific criteria.
 *
 * @author Sushan Khatiwada
 * @version 1.0
 */
final class TableCreator
{
    /**
     * Database connection
     *
     * @var PDO
     */
    private $db;

    /**
     * Constructor.
     * Initializes the database connection and executes create() and fill() methods.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->create();
        $this->fill();
    }

    /**
     * Creates the 'Test' table with specific fields.
     *
     * @access private
     */
    private function create()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS Test (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        script_name VARCHAR(25),
                        start_time DATETIME,
                        end_time DATETIME,
                        result ENUM('normal', 'illegal', 'failed', 'success')
                    )";

            $this->db->exec($sql);
            echo "Table created successfully\n";
        } catch (PDOException $e) {
            echo "Error creating table: " . $e->getMessage();
        }
    }

    /**
     * Fills the 'Test' table with random data.
     *
     * @access private
     */
    private function fill()
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO Test (script_name, start_time, end_time, result) VALUES (:script_name, :start_time, :end_time, :result)");

            // Generate and insert random data
            for ($i = 0; $i < 1000; $i++) {
                $scriptName = $this->generateRandomString(25);
                $startTime = date('Y-m-d H:i:s', rand(strtotime('2021-01-01'), strtotime('2022-01-01')));
                $endTime = date('Y-m-d H:i:s', strtotime($startTime . ' + ' . rand(1, 30) . ' minutes'));
                $result = rand(0, 1) ? 'normal' : 'success';

                $stmt->bindParam(':script_name', $scriptName);
                $stmt->bindParam(':start_time', $startTime);
                $stmt->bindParam(':end_time', $endTime);
                $stmt->bindParam(':result', $result);
                $stmt->execute();
            }
            echo "Table filled with random data\n";
        } catch (PDOException $e) {
            echo "Error filling table: " . $e->getMessage();
        }
    }

    /**
     * Generates a random string of specified length.
     *
     * @param int $length Length of the random string
     * @return string Random string
     */
    private function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Retrieves data from the 'Test' table based on the specified criteria.
     *
     * @return array Fetched data
     */
    public function get()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Test WHERE result IN ('normal', 'success')");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }
}

// Database connection configuration
$servername = "localhost";
$username = "username";
$password = "password";
$database = "database_name";

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Example usage of the class
    $tableCreator = new TableCreator($db);
    $data = $tableCreator->get();
    print_r($data);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
