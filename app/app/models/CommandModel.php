<?php

class CommandModel extends BaseModel
{
    private $id;
    private $prevalue;
    private $postvalue;
    private $name;
    private $comment;
    private $enable;

    /**
     * Alle Daten holen
     *
     * @return array[]
     */
    public function getCommands()
    {
        // Query setzen
        $this->db->query("SELECT * FROM Command");
        $results = $this->db->resultSet();
        return $results;
    }

    /**
     * Daten per ID holen
     *
     * @param $commandid
     * @return array[]
     */
    public function getCommandByID($commandid)
    {
        // Query setzen
        $this->db->query("SELECT * FROM Command where id = :commandid");
        // Daten für Query setzen
        $this->db->bind(':commandid', $commandid);
        $results = $this->db->single();
        return $results;
    }

    /**
     * Daten hinzufügen
     *
     * @param $data
     * @return boolean
     */
    public function insertData($data)
    {
        // Enable setzen
        if ($data['enable'] == 2) {
            $data['enable'] = true;
        } else {
            $data['enable'] = false;
        }
        // Query setzen
        $this->db->query("INSERT INTO Command (prevalue, postvalue, name, comment, enable) VALUE (:prevalue, :postvalue, :name, :comment, :enable)");
        // Daten für Query setzen
        $this->db->bind(':prevalue', $data['prevalue']);
        $this->db->bind(':postvalue', $data['postvalue']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':enable', $data['enable']);
        $result = $this->db->execute();
        return $result;
    }

    /**
     * Daten anhand der ID aktualisieren
     *
     * @param $data
     * @return boolean
     */
    public function updateData($data)
    {
        // Setzt für bestimmte Werte eine Zahl ein
        if ($data['enable'] == 2) {
            $data['enable'] = true;
        } else {
            $data['enable'] = false;
        }
        // Query setzen
        $this->db->query("UPDATE Command SET prevalue = :prevalue, postvalue = :postvalue, name = :name, comment = :comment, enable = :enable where id = :id");
        // Daten für Query setzen
        $this->db->bind(':prevalue', $data['prevalue']);
        $this->db->bind(':postvalue', $data['postvalue']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':enable', $data['enable']);
        $this->db->bind(':id', $data['id']);
        $result = $this->db->execute();
        return $result;
    }


    /**
     * Daten anhand der ID löschen
     *
     * @param $commandid
     * @return boolean
     */
    public function deleteByID($commandid)
    {
        // Query setzen
        $this->db->query("DELETE FROM Command where id = :commandid");
        // Daten für Query setzen
        $this->db->bind(':commandid', $commandid);
        $result = $this->db->execute();
        return $result;
    }
}
