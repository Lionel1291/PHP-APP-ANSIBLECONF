<?php


class Configadmin extends Controller
{

    /**
     * Alle Konfigurationen werden angezeigt und anschliessend über configadmin/index.twig.html dargestellt
     *
     * @param string $pagename
     */
    public function index($pagename = 'Adminauflistung')
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Daten abrufen
        $configModel = $this->model('ConfigModel');
        $commandModel = $this->model('CommandModel');
        $commandArray = $commandModel->getCommands();
        $configArray = $configModel->getConfigs();

        // Daten für GUI auffrischen
        $data = $configModel->renderConfigList4GUI($commandArray, $configArray);

        echo $this->twig->render('configadmin/index.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data]);
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
     * Hier werden Daten gelöscht
     */
    public function delete()
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Wenn der Aufruf Post ist
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Daten trimmen
            $id = trim(
                filter_input(INPUT_POST, 'delete', FILTER_SANITIZE_NUMBER_INT)
            );
            // Wenn die ID gesetzt ist wenn nicht wird man auf /Configadmin zurückgewiesen
            if (empty($id)) {
                redirect("Configadmin");
            }
            // Daten werden über das Model gelöscht
            $configModel = $this->model('ConfigModel');
            if ($configModel->deleteByID($id)) {
                redirect("Configadmin");
            } else {
                // Aufruf der Fehlerseite
                $message = "Leider konnte die Config nicht gelöscht werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                echo $this->twig->render('configadmin/failureConfigadmin.twig.html', ['title' => 'Delete Config as Admin Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
            }
        } else {
            // Aufruf nicht POST man wird zurückgesiesen auf /Configadmin
            redirect("Configadmin");
        }
    }

    /**
     * Damit kann man die Konfig als YAML anscheun und hat die Möglichkeit die Config zu berstätigen oder abzulehen
     * Es wird configadmin/view.twig.html aufgerufen
     *
     * @param string $pagename
     */
    public function view($pagename = 'View')
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Ist der Aufruf Post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = trim(
                filter_input(INPUT_POST, 'view', FILTER_SANITIZE_NUMBER_INT)
            );

            // Wenn keine ID vorhanden wird man auf /Configadmin umgeleitet
            if (empty($id)) {
                redirect("Configadmin");
            }
            // Daten werden abgerufen und danach die View geladen
            $commandModel = $this->model('CommandModel');
            $configModel = $this->model('ConfigModel');
            $commandid = $configModel->getCommandIDByID($id)['commandid'];
            if (empty($commandid)) {
                // Aufruf der Fehlerseite
                $message = "Leider konnte die Config nicht geladen werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                echo $this->twig->render('configadmin/failureConfigadmin.twig.html', ['title' => 'View Config as Admin Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                exit();
            }
            $command = $commandModel->getCommandByID($commandid);
            $yaml = $configModel->renderConfigAnsible4GUI($id, $command);
            echo $this->twig->render('configadmin/view.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'yaml' => $yaml, 'id' => $id]);
        } else {
            // Wenn der aufruf nicht Post ist wird man zurcügewiesen auf /Configadmin
            redirect("Configadmin");
        }
    }

    /**
     * Hier werden die Danten für eine änderung Validiert
     *
     */
    public function change()
    {
        // Ruft User prüf funktion auf
        $this->checkUser();

        // Ist der Aufruf Post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $approved = "";
            $comment = "";
            $comment_err = "";
            // Prüfen ob Post Parameter gesetzt sind wenn nciht umleitung auf /COnfigadmin
            if (isset($_POST['commit']) || isset($_POST['dismiss'])) {
                // Daten trimmen
                $comment = trim(
                    filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING)
                );
                if (empty($comment)) {
                    $comment_err = 'Bitte eine Begründung eintragen';
                }

                // Je nach Postparameter wird ein anderer Wert eingetragen
                if (isset($_POST['commit'])) {
                    $approved = 2;
                    $id = trim(
                        filter_input(INPUT_POST, 'commit', FILTER_SANITIZE_NUMBER_INT)
                    );
                } else {
                    $approved = 3;
                    $id = trim(
                        filter_input(INPUT_POST, 'dismiss', FILTER_SANITIZE_NUMBER_INT)
                    );
                }
            } else if (isset($_POST['cancel'])) {
                redirect("Configadmin");
            }

            // Prüft ob die Wert gesetzt sonst umleitung auf /Configadmin
            if (empty($id)) {
                redirect("Configadmin");
            }

            // Wenn keine Fehler vorhanden Daten werden geändert
            // Wenn Fehler vorhanden wird nich einmal configadmin/view.twig.html aufgerufen
            if (empty($comment_err)) {
                $configModel = $this->model('ConfigModel');
                if ($configModel->changeAdmin($id, $comment, $approved)) {
                    redirect("Configadmin");
                } else {
                    // Aufruf der Fehlerseite
                    $message = "Leider konnte die Config nicht bearbeitet werden, prüfen Sie ob die Config noch vorhanden ist. Bitte kontaktieren Sie ansonsten den Administrator:";
                    echo $this->twig->render('configadmin/failureConfigadmin.twig.html', ['title' => 'Change Config as Admin Error', 'urlroot' => URLROOT, 'data' => $message, 'mail' => $this->mail]);
                }
            } else {
                $commandModel = $this->model('CommandModel');
                $configModel = $this->model('ConfigModel');
                $command = $commandModel->getCommandByID($configModel->getCommandIDByID($id)['commandid']);
                $yaml = $configModel->renderConfigAnsible4GUI($id, $command);
                echo $this->twig->render('configadmin/view.twig.html', ['title' => 'View', 'urlroot' => URLROOT, 'yaml' => $yaml, 'id' => $id, 'comment_err' => $comment_err]);
            }
        } else {
            // Der Aufruf ist nicht Post umleitung auf /Configadmin
            redirect("Configadmin");
        }
    }


}
