<?php

require_once("prepend.php");

$session->removeAttribute(SESSION_LOGGED_USER_ATTR);
Header("Location: /admin");

?>