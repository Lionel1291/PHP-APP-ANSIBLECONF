<?php

// DB-Params (die Werte sind im Docker-Compose definiert)
define('DB_HOST', 'mysql');
define('DB_USER', 'ansibleUser');
define('DB_PASS', 'AUPasswd987123');
define('DB_NAME', 'ansibleconfig');

// Unsere APP-Root 
define('APPROOT', dirname(dirname(__FILE__)));

// Unsere URL-Root
define('URLROOT', 'http://localhost:8000');

// Zeitzone setzen
date_default_timezone_set("Europe/Zurich");
