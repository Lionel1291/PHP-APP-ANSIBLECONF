<?php


class Command extends Controller
{
    /**
     * Commands anzeigen mit command/index.twig.html
     *
     * @param string $pagename
     */
    public function index($pagename = 'Command Auflistung')
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Dante abrufen
        $commandModel = $this->model('CommandModel');
        $data = $commandModel->getCommands();
        echo $this->twig->render('command/index.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
    }

    /**
     * Funktion, um zu prüfen, ob der User hier überhaubpt sein darf und eingeloggt ist.
     *
     * @return : void()
     */
    private function checkUser()
    {
        if (!isset($_SESSION['user_id'])) {
            // Nicht eingeloggt, redirect auf Login
            redirect('Users/login');
        } else {

            if (!in_array("admin", $_SESSION['user_roles'])) {
                // Wir sind eingeloggt, aber nicht Admin: Weg von hier
                redirect('Config');
            }
        }
    }

    /**
     * Daten hinzufügen und aufruf von command/add.twig.html
     *
     * @param string $pagename
     */
    public function add($pagename = 'Add Command')
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        $commandModel = $this->model('CommandModel');
        // Prüfen ob aufruf Post ist sonst command/add.twig.html laden
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Daten trimmen
            $name = trim(
                filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)
            );
            $prevalue = trim(
                filter_input(INPUT_POST, 'prevalue', FILTER_SANITIZE_STRING)
            );
            $postvalue = trim(
                filter_input(INPUT_POST, 'postvalue', FILTER_SANITIZE_STRING)
            );
            $enable = trim(
                filter_input(INPUT_POST, 'enable', FILTER_SANITIZE_NUMBER_INT)
            );
            $comment = trim(
                filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING)
            );

            // Getrimmte Daten setzen
            $data = [
                'name' => $name,
                'name_err' => '',
                'prevalue' => $prevalue,
                'prevalue_err' => '',
                'postvalue' => $postvalue,
                'enable' => $enable,
                'enable_err' => '',
                'comment' => $comment
            ];

            // Validierung der Daten
            if (empty($data['name'])) {
                $data['name_err'] = 'Bitte Name angeben';
            }

            if (empty($data['prevalue'])) {
                $data['prevalue_err'] = 'Bitte Prevalue angeben';
            }

            if (empty($data['enable']) && $data['enable'] != 2 && $data['enable'] != 3) {
                $data['enable_err'] = 'Bitte Option auswählen';
            }

            // Keine Errors vorhanden
            if (empty($data['name_err']) && empty($data['prevalue_err']) && empty($data['enable_err'])) {
                if ($commandModel->insertData($data)) {
                    redirect("Command");
                } else {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte der Command nicht hinzugefügt werden, versuchen Sie es allenfalls erneut. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('command/failureCommand.twig.html', ['title' => 'Insert Command Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                }
            } else {
                // Fehler vorhanden erneutes laden von command/add.twig.html
                echo $this->twig->render('command/add.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
            }
        } else {
            // Error und Daten auf nichts setzen
            $data = [
                'name' => '',
                'name_err' => '',
                'prevalue' => '',
                'prevalue_err' => '',
                'postvalue' => '',
                'enable' => '',
                'enable_err' => '',
                'comment' => ''
            ];
            echo $this->twig->render('command/add.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
        }
    }

    /**
     * Command ändern
     *
     * @param string $pagename
     */
    public function edit($pagename = 'Edit Command')
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        $commandModel = $this->model('CommandModel');

        // Prüfen, ob der Aufruf Post ist
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Prüfen ob Post Wert edit gesetzt ist
            if (isset($_POST['edit'])) {
                // Daten mit ID laden
                $commandArray = $commandModel->getCommandByID($_POST['edit']);
                if (empty($commandArray)) {
                    $message = "Leider konnte der Command nicht geladen werden, prüfen Sie ob der Command noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('command/failureCommand.twig.html', ['title' => 'Edit Command Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                    exit();
                }
                // Daten für Twig formatieren
                if ($commandArray['enable']) {
                    $commandArray['enable'] = 2;
                } else {
                    $commandArray['enable'] = 3;
                }
                // Daten setzen
                $data = [
                    'id' => $commandArray['id'],
                    'name' => $commandArray['name'],
                    'name_err' => '',
                    'prevalue' => $commandArray['prevalue'],
                    'prevalue_err' => '',
                    'postvalue' => $commandArray['postvalue'],
                    'enable' => $commandArray['enable'],
                    'enable_err' => '',
                    'comment' => $commandArray['comment'],
                    'edit_err' => 'enter'
                ];
            } else {
                // Daten trimmen
                $name = trim(
                    filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)
                );
                $prevalue = trim(
                    filter_input(INPUT_POST, 'prevalue', FILTER_SANITIZE_STRING)
                );
                $postvalue = trim(
                    filter_input(INPUT_POST, 'postvalue', FILTER_SANITIZE_STRING)
                );
                $enable = trim(
                    filter_input(INPUT_POST, 'enable', FILTER_SANITIZE_NUMBER_INT)
                );
                $comment = trim(
                    filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING)
                );
                // ID setzen un danach Daten setzen
                $id = $_POST['id'];
                $data = [
                    'id' => $id,
                    'name' => $name,
                    'name_err' => '',
                    'prevalue' => $prevalue,
                    'prevalue_err' => '',
                    'postvalue' => $postvalue,
                    'enable' => $enable,
                    'enable_err' => '',
                    'comment' => $comment
                ];
            }

            // Daten Validieren
            if (empty($data['name'])) {
                $data['name_err'] = 'Bitte Name angeben';
            }

            if (empty($data['prevalue'])) {
                $data['prevalue_err'] = 'Bitte Prevalue angeben';
            }

            if (empty($data['enable']) && $data['enable'] != 2 && $data['enable'] != 3) {
                $data['enable_err'] = 'Bitte Option auswählen';
            }

            // Prüfen ob Daten fehlerhaft
            if (empty($data['name_err']) && empty($data['prevalue_err']) && empty($data['enable_err']) && empty($data['edit_err'])) {
                // Daten I.o, Daten ändern, wenn Fehlschlägt redirect
                if ($commandModel->updateData($data)) {
                    redirect("Command");
                } else {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte der Command nicht editiert werden, prüfen Sie ob der Command noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('command/failureCommand.twig.html', ['title' => 'Edit Command Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                }
            } else {
                // Fehler gefunden erneutes laden von command/edit.twig.html
                echo $this->twig->render('command/edit.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
            }
        } else {
            // Wenn der Aufruf nicht Post umleitung auf /Commadn
            redirect("Command");
        }
    }

    /**
     * Command löschen
     */
    public function delete()
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Prüfen, ob aufruf Post ist sonst Umleitung auf /Command
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Daten trimmen
            $id = trim(
                filter_input(INPUT_POST, 'delete', FILTER_SANITIZE_NUMBER_INT)
            );
            // Prüfen ob Daten vorhanden sonst Umleitung auf /Command
            if (empty($id)) {
                redirect("Command");
            }
            $commandModel = $this->model('CommandModel');
            if ($commandModel->deleteByID($id)) {
                redirect("Command");
            } else {
                // Aufruf der Fehlerseite
                $message = "Leider konnte der Command nicht gelöscht werden, prüfen Sie ob der Command noch in einer Konfiguration vorhanden ist und löschen respektive editieren Sie diese zuerst. Bitte kontaktieren Sie ansonsten den Administrator:";
                echo $this->twig->render('command/failureCommand.twig.html', ['title' => 'Delete Command Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
            }
            $commandModel->deleteByID($id);
        } else {
            redirect("Command");
        }
    }
}
