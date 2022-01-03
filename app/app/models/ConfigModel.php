<?php

class ConfigModel extends BaseModel
{
    private $id;
    private $version;
    private $commandid;
    private $approved; // Wert '' = Nicht überprüft, Wert 2 = Bestätigt, Wert 3 = Abgelehnt
    private $userid;
    private $lastmodify;
    private $create;
    private $hosts;
    private $commandvalue;
    private $name;
    private $comment;

    /**
     * Alle Configs holen
     *
     * @return array[]
     */
    public function getConfigs()
    {
        // Query setzen
        $this->db->query("SELECT *, DATE_FORMAT(`create`, '%d.%m.%Y %H:%i:%s') as `create`, DATE_FORMAT(lastmodify,'%d.%m.%Y %H:%i:%s') as lastmodify FROM Config");
        $results = $this->db->resultSet();
        return $results;
    }

    /**
     * Configs anhand der UserID holen
     *
     * @param $userid
     * @return array[]
     */
    public function getConfigByUserID($userid)
    {
        // Query setzen
        $this->db->query("SELECT *, DATE_FORMAT(`create`, '%d.%m.%Y %H:%i:%s') as `create`, DATE_FORMAT(lastmodify,'%d.%m.%Y %H:%i:%s') as lastmodify FROM Config where userid = :userid");
        // Daten für Query setzen
        $this->db->bind(':userid', $userid);
        $results = $this->db->resultSet();
        return $results;
    }

    /**
     * CommandID anhand der ID holen
     *
     * @param $configid
     * @return array[]
     */
    public function getCommandIDByID($configid)
    {
        // Query setzen
        $this->db->query("SELECT commandid FROM Config where id = :configid");
        // Daten für Query setzen
        $this->db->bind(':configid', $configid);
        $result = $this->db->single();
        return $result;
    }

    /**
     * Daten werden für die GUI aufbreitet
     *
     * @param $commandArray
     * @param $configArray
     * @return array[]
     */
    public function renderConfigList4GUI($commandArray, $configArray)
    {
        $data = [];
        foreach ($configArray as $config) {
            $configrow = [];
            foreach ($config as $key => $value) {

                if ($key == 'commandid') {

                    foreach ($commandArray as $command) {

                        if ($command['id'] == $value) {
                            $configrow['command'] = $command['name'];
                        }
                    }
                }

                $configrow[$key] = $value;
            }

            array_push($data, $configrow);
        }

        return $data;
    }

    /**
     * Daten per ID löschen
     *
     * @param $configid
     * @return boolean
     */
    public function deleteByID($configid)
    {
        // Query setzen
        $this->db->query("DELETE FROM Config where id = :configid");
        // Daten für Query setzen
        $this->db->bind(':configid', $configid);
        $result = $this->db->execute();
        return $result;
    }

    /**
     * Daten hinzufügen
     *
     * @param $data
     * @param $userid
     * @return boolean
     */
    public function insertData($data, $userid)
    {
        // Datum setzen auf jetzt
        $now = date("Y-m-d H:i:s");
        // Query setzen
        $this->db->query("INSERT INTO Config (version, approved, lastmodify, `create`, hosts, commandvalue, name, userid, commandid) VALUE (1.0, '',:lastmodify,:create, :hosts, :commandvalue, :name, :userid, :commandid)");
        // Daten für Query setzen
        $this->db->bind(':lastmodify', $now);
        $this->db->bind(':create', $now);
        $this->db->bind(':hosts', $data['hosts']);
        $this->db->bind(':commandvalue', $data['commandvalue']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':commandid', $data['commandid']);
        $this->db->bind(':userid', $userid);
        $result = $this->db->execute();
        return $result;
    }

    /**
     * Daten aktualisieren
     *
     * @param $data
     * @return boolean
     */
    public function updateData($data)
    {
        // Datum setzen auf jetzt
        $now = date("Y-m-d H:i:s");
        // Version um 0.1 erhöhen
        $data['version'] += 0.1;
        // Query setzen
        $this->db->query("UPDATE Config SET version = :version, approved = '', lastmodify = :lastmodify, hosts = :hosts, commandvalue = :commandvalue, name = :name, comment = '', commandid = :commandid where id = :id");
        // Daten für Query setzen
        $this->db->bind(':version', $data['version']);
        $this->db->bind(':lastmodify', $now);
        $this->db->bind(':hosts', $data['hosts']);
        $this->db->bind(':commandvalue', $data['commandvalue']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':commandid', $data['commandid']);
        $this->db->bind(':id', $data['id']);
        $result = $this->db->execute();
        return $result;
    }

    /**
     * Daten werden in YAML Format gerendert und als String zurückgegeben
     *
     * @param $configid
     * @param $commandArray
     * @return string
     */
    public function renderConfigAnsible4GUI($configid, $command)
    {
        $config = $this->getConfigByID($configid);
        // Array wird für YAML emit vorbereitet
        $data = array(
            array(
                'name' => $config['name'],
                'hosts' => $config['hosts'],
                'tasks' => array(
                    array(
                        'name' => $command['name'],
                        'community.network.aruba_config' => array(
                            'lines' => array(
                                $command['prevalue'] . ' ' . $config['commandvalue'] . ' ' . $command['postvalue']
                            )
                        )
                    )
                )
            )
        );
        // yaml_emit macht ändert die Daten in das YAML Format
        $yaml = yaml_emit($data);
        return $yaml;
    }

    /**
     * Config anhand der ID holen
     *
     * @param $configid
     * @return array[]
     */
    public function getConfigByID($configid)
    {
        // Query setzen
        $this->db->query("SELECT *, DATE_FORMAT(`create`, '%d.%m.%Y %H:%i:%s') as `create`, DATE_FORMAT(lastmodify,'%d.%m.%Y %H:%i:%s') as lastmodify FROM Config where id = :configid");
        // Daten für Query setzen
        $this->db->bind(':configid', $configid);
        $result = $this->db->single();
        return $result;
    }

    /**
     * Daten werden auf der DB per ID aktualisiert
     *
     * @param $configid
     * @param $comment
     * @param $newapproved
     * @return boolean
     */
    public function changeAdmin($configid, $comment, $newapproved)
    {
        // Query setzen
        $this->db->query("UPDATE Config SET approved = :approved, comment = :comment where id = :id");
        // Daten für Query setzen
        $this->db->bind(':approved', $newapproved);
        $this->db->bind(':comment', $comment);
        $this->db->bind(':id', $configid);
        $result = $this->db->execute();
        return $result;
    }
}
