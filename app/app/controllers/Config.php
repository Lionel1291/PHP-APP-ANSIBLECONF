<?php


class Config extends Controller
{
    private $configArray = array();
    private $commandArray = array();

    /**
     * Beim zugriff auf /Config wird die View config/index.twig.html aufgerufen
     * @param string $pagename
     */
    public function index($pagename = 'Ansible Auflistung')
    {
        // Ruft Login Ckeck prüf funktion auf
        $this->checkLogin();

        // Daten werden aus den Models abgerufen
        $configModel = $this->model('ConfigModel');
        $commandModel = $this->model('CommandModel');
        $commandArray = $commandModel->getCommands();
        $configArray = $configModel->getConfigByUserID($_SESSION['user_id']);

        // Abruf gerendertet Daten
        $data = $configModel->renderConfigList4GUI($commandArray, $configArray);
        echo $this->twig->render('config/index.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
    }

    /**
     * Diese funktion prüft, ob der User eingeloggt ist
     */
    private function checkLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            // Nicht eingeloggt, redirect auf Login
            redirect('Users/login');
        }
    }

    /**
     * Hier werden Daten hinzugefügt über die View config/add.twig.html
     *
     * @param string $pagename
     */
    public function add($pagename = 'Add Config')
    {
        // Ruft Login Ckeck prüf funktion auf
        $this->checkLogin();

        // Daten werden aus den Models abgerufen
        $configModel = $this->model('ConfigModel');
        $commandModel = $this->model('CommandModel');
        $commandArray = $commandModel->getCommands();

        // Wenn der Aufruf Post ist
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Daten nach Datentyp trimmen
            $name = trim(
                filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)
            );
            $hosts = trim(
                filter_input(INPUT_POST, 'hosts', FILTER_SANITIZE_STRING)
            );

            $commandvalue = $_POST['commandvalue'];

            $commandid = trim(
                filter_input(INPUT_POST, 'commandid', FILTER_SANITIZE_NUMBER_INT)
            );

            // Daten in Array setzen
            $data = [
                'name' => $name,
                'name_err' => '',
                'hosts' => $hosts,
                'hosts_err' => '',
                'commandvalue' => $commandvalue,
                'commandvalue_err' => '',
                'commandid' => $commandid,
                'commandid_err' => ''
            ];


            // Validierung der Daten
            if (empty($data['name'])) {
                $data['name_err'] = 'Bitte Name angeben';
            }

            if (empty($data['hosts'])) {
                $data['hosts_err'] = 'Bitte Hosts angeben';
            }

            if (empty($data['commandvalue'])) {
                $data['commandvalue_err'] = 'Bitte Command auswählen';
            }

            if (empty($data['commandid'])) {
                $data['commandid_err'] = 'Bitte Command auswählen';
            }

            // Keine Errors vorhanden
            if (empty($data['name_err']) && empty($data['hosts_err']) && empty($data['commandvalue_err']) && empty($data['commandid_err'])) {
                // Daten später in DB schreiben
                if ($configModel->insertData($data, $_SESSION['user_id'])) {
                    redirect("Config");
                } else {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte die Config nicht hinzugefügt werden, versuchen Sie es allenfalls erneut. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('config/failureConfig.twig.html', ['title' => 'Insert Config Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                }
            } else {
                // Mindestens 1 Datensatz nicht i.o., nochmal ausfüllen / überarbeiten
                echo $this->twig->render('config/add.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data, 'commands' => $commandArray]);
            }
        } else {
            // Daten und Fehlermeldungen auf nichts setzen
            $data = [
                'name' => '',
                'name_err' => '',
                'hosts' => '',
                'hosts_err' => '',
                'commandvalue' => '',
                'commandvalue_err' => '',
                'commandid' => '',
                'commandid_err' => ''
            ];

            echo $this->twig->render('config/add.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data, 'commands' => $commandArray]);
        }
    }

    /**
     * Hier werden bestehende Daten überarbeitet indem config/edit.twig.html aufgerufen wird
     *
     * @param string $pagename
     */
    public function edit($pagename = 'Edit Config')
    {
        // Ruft Login Ckeck prüf funktion auf
        $this->checkLogin();

        // Daten werden aus den Models abgerufen
        $configModel = $this->model('ConfigModel');
        $commandModel = $this->model('CommandModel');
        $commandArray = $commandModel->getCommands();

        // Wenn der aufruf Post ist
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Prüfen, ob der Post Wert edit gesetzt ist
            if (isset($_POST['edit'])) {

                // Daten über ID abrufen
                $configArray = $configModel->getConfigByID($_POST['edit']);
                if (empty($configArray)) {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte die Config nicht geladen werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('config/failureConfig.twig.html', ['title' => 'Edit Config Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                    exit();
                }
                // Daten setzen und Fehlermeldungen auf 0
                $data = [
                    'name' => $configArray['name'],
                    'name_err' => '',
                    'hosts' => $configArray['hosts'],
                    'hosts_err' => '',
                    'commandvalue' => $configArray['commandvalue'],
                    'commandvalue_err' => '',
                    'commandid' => $configArray['commandid'],
                    'commandid_err' => '',
                    'comment' => $configArray['comment'],
                    'edit_err' => 'enter',
                    'version' => $configArray['version'],
                    'id' => $configArray['id'],
                    'userid' => $configArray['userid'],
                ];
            } // Wenn der Parameter nicht gesetzt ist
            else {
                // Daten trimmen
                $name = trim(
                    filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)
                );
                $hosts = trim(
                    filter_input(INPUT_POST, 'hosts', FILTER_SANITIZE_STRING)
                );

                $commandvalue = $_POST['commandvalue'];

                $commandid = trim(
                    filter_input(INPUT_POST, 'commandid', FILTER_SANITIZE_NUMBER_INT)
                );

                $userid = trim(
                    filter_input(INPUT_POST, 'userid', FILTER_SANITIZE_NUMBER_INT)
                );

                $version = trim(
                    filter_input(INPUT_POST, 'version', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC)
                );

                $id = trim(
                    filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT)
                );

                $comment = trim(
                    filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING)
                );

                // Daten setzen
                $data = [
                    'name' => $name,
                    'name_err' => '',
                    'hosts' => $hosts,
                    'hosts_err' => '',
                    'version' => $version,
                    'id' => $id,
                    'userid' => $userid,
                    'commandvalue' => $commandvalue,
                    'commandvalue_err' => '',
                    'commandid' => $commandid,
                    'commandid_err' => '',
                    'comment' => $comment
                ];


                // Validierung der Daten
                if (empty($data['name'])) {
                    $data['name_err'] = 'Bitte Name angeben';
                }

                if (empty($data['hosts'])) {
                    $data['hosts_err'] = 'Bitte Hosts angeben';
                }

                if (empty($data['commandvalue'])) {
                    $data['commandvalue_err'] = 'Bitte Command auswählen';
                }

                if (empty($data['commandid'])) {
                    $data['commandid_err'] = 'Bitte Command auswählen';
                }
            }

            // Keine Errors vorhanden
            if (empty($data['name_err']) && empty($data['hosts_err']) && empty($data['commandvalue_err']) && empty($data['commandid_err']) && empty($data['edit_err'])) {
                // Prüft ob die Datenbank true für die Operation zurückgibt
                if ($configModel->updateData($data)) {
                    redirect("Config");
                } else {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte die Config nicht editiert werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('config/failureConfig.twig.html', ['title' => 'Edit Config Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                }
            } else {
                // Fehler vorhanden die View wird nochmal geladen
                echo $this->twig->render('config/edit.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data, 'commands' => $commandArray]);
            }
        } else {
            // Weil der Aufruf nicht Post ist wir man zurückgewiesen
            redirect('Config');
        }
    }

    /**
     * Hier werden die Daten im YAML Format angezeigt für die Ansible Config
     *
     * @param string $pagename
     */
    public function view($pagename = 'View')
    {
        // Ruft Login Ckeck prüf funktion auf
        $this->checkLogin();

        $configModel = $this->model('ConfigModel');
        // Prüfen, ob der Aufruf Post ist
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['view'])) {
            $configArray = $configModel->getConfigByID($_POST['view']);
            // Prüfen ob die Konfiguration so abgesegnet wurde
            if (empty($configArray)) {
                // Aufruf der Fehlerseite
                $message = "Leider konnte die Config nicht geladen werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                echo $this->twig->render('config/failureConfig.twig.html', ['title' => 'View Config Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                exit();
            }
            if ($configArray['approved'] == 2) {
                // Daten werden aus den Models abgerufen
                $commandModel = $this->model('CommandModel');
                $command = $commandModel->getCommandByID($configModel->getCommandIDByID($_POST['view'])['commandid']);
                $yaml = $configModel->renderConfigAnsible4GUI($_POST['view'], $command);
                // Daten werden über config/view.twig.html angezeigt
                echo $this->twig->render('config/view.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'yaml' => $yaml]);
            } else {
                // Umleistung auf /Config weil die Überprüfung nicht erfolgt respektive abgelehnt wurde
                redirect('Config');
            }
        } else {
            // Weil der Aufruf nicht Post ist wir man zurückgewiesen
            redirect('Config');
        }
    }
}
