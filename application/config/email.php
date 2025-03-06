<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


$config['protocol']    = 'smtp';
$config['smtp_host']   = 'ssl://smtp.gmail.com'; // or 'tls://smtp.gmail.com'
$config['smtp_port']   = 465; // Use 587 if using TLS
$config['smtp_user']   = 'roreplayreplay@gmail.com';
$config['smtp_pass']   = 'ygfljkibrbnpoaqj'; // Use an App Password if 2FA is enabled
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['smtp_timeout'] = 30;


// defined('BASEPATH') OR exit('No direct script access allowed');

// $config['protocol'] = 'mail';  // Use the 'mail' protocol instead of 'smtp'
// $config['mailtype'] = 'html';
// $config['charset'] = 'utf-8';
// $config['wordwrap'] = TRUE;
// $config['newline'] = "\r\n";  // Ensure proper line breaks
