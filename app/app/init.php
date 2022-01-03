<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Die Config-Variablen unserer App
require_once 'config/config.php';

// Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';

// Die Main-Klassen unserer App
require_once 'core/App.php';
require_once 'core/Controller.php';
require_once 'core/Database.php';
require_once 'core/BaseModel.php';




