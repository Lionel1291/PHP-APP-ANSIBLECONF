<?php

/**
 * PDO Datenbankklasse
 *
 * - Connect to Datebase
 * - Erstellt Prepared Statements
 * - Bind Values
 * - Retourniert Rows und Results
 */

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;


    private $dbh;
    private $stmt;
    private $error;

    public function __construct()
    {
        // Set DSN - Verbindungsstring auf den Server
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ";charset=utf8";
        $options = array(
            // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // ErrorHandling, wird in der Produktivumgebung nicht benötigt
            PDO::FETCH_OBJ // Wir wollen die Resultate als Objekte, und nicht als Arrays
        );

        // Instanz erstellen
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            redirect("Home/main");
        }
    }

    // Statement vorbereiten mit Query
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind Values
    public function bind($param, $value, $type = null)
    {
        // Falls der Type nicht mitgegeben wird - überprüfen
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Ausführen der Prep-Statements

    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Das Ergebnis als Array aus Objekten

    public function execute()
    {
        return $this->stmt->execute();
    }

    // Das Ergebnis als Einzelnes Objekt

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Anzahl der Resultate // RowCount
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

}
