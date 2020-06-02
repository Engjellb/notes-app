<?php

return "
        <h3>Confirmation for registration</h3>
        <p>You are registred with these data</p>
        <table style=\"width: 100%; border: 2px solid black\">
            <tr>
            <th>Name and Surname</th>
            <td>" . $firsname . "</td>
        </tr>
        <tr>
            <th>Emaili</th>
            <td>" . $email . "</td>
        </tr>
        </table>
        
        <p>Click this link to activate account: <a href='$link'>Click</a></p>
        <p>If above link does not work please copy below link to browser</p>
        <p>".$link."</p>";

