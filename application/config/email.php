<?php 


// $config['protocol'] = 'smtp';
// $config['smtp_host'] = 'smtp.gmail.com'; // Replace with your SMTP server sandbox.smtp.mailtrap.io
// $config['smtp_user'] = 'roreplayreplay@gmail.com'; // Replace with your SMTP email d4d05e583650c9
// $config['smtp_pass'] = 'ygfljkibrbnpoaqj'; 
// // Replace with your SMTP password 2203dbcc9f95c3 ygfljkibrbnpoaqj
// $config['smtp_port'] = 465; // Use 465 for SSL or 587 for TLS
// $config['smtp_crypto'] = 'ssl'; // Use 'ssl' if needed
// $config['mailtype'] = 'html';
// $config['charset'] = 'utf-8';
// $config['wordwrap'] = TRUE;
// $config['newline'] = "\r\n";
  
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
