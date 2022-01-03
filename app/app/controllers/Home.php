<?php

class Home extends Controller
{

    /**
     * Wenn man auf den Index zugreift wird man auf /Config weitergeleitet
     *
     * @return : void
     */
    public function index()
    {
        redirect('Config');
    }

    /**
     * Dient als schwerwiegende Fehler abfang Quelle
     */
    public function main($pagename = '505 Error')
    {
        $data = "Leider ist ein unerwarteter Fehler aufgetreten, bitte kontaktieren Sie den Administrator:";
        echo $this->twig->render('home/505.twig.html', ['title' => $pagename, 'urlroot' => URLROOT, 'data' => $data, 'mail' => $this->mail]);
    }
}
