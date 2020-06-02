<?php

function getSession() {
    $timeDuration = 300;// 5 minutes
    if(isset($_SESSION['loggedTime'])) {
        if((time() - $_SESSION['loggedTime']) > $timeDuration) {
            foreach ($_SESSION as $key => $value) {
                if($key !== 'sessionExpired') {
                    unset($_SESSION[$key]);
                }
            }
            $_SESSION['sessionExpired'] = 'Your session has expired';
            return header('Location: /login/loginForm');
        }
        $_SESSION['loggedTime'] = time();
    }
}