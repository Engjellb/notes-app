<?php

namespace app\Http\Controllers;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class BaseController
{
    protected $twig;
    protected $conn;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../resources/views');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false
        ]);

        $config = include "../config/database.php";
        $this->conn = new \mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        if($this->conn->connect_error) {
            die('Connection failed: '.$this->conn->connect_error);
        } else {
            $this->conn->set_charset('utf8');
        }
    }

    public function sendEmail($email, $name, $subject, $body) {
        $config = include '../config/mail.php';

        // Create the Transport
        $transport = (new Swift_SmtpTransport($config['mail_server'], $config['mail_port']))
            ->setUsername($config['mail_username'])
            ->setPassword($config['mail_password']);

// Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

// Create a message
        $message = (new Swift_Message($subject))
            ->setFrom(['noreply@noreply.com' => 'Engjell Beselica'])
            ->setTo([$email => $name])
            ->setBody(strip_tags($body))
            ->addPart($body, 'text/html');

// Send the message
        return $mailer->send($message);
    }

    protected function view($template, $data = []) {
        $data['isAuth'] = isset($_SESSION['isAuth']) ? $_SESSION['isAuth'] : false;
        $data['isAdmin'] = $data['isAuth'] ? $_SESSION['isAdmin'] : false;
        echo $this->twig->render($template.'.html', $data);
    }
}