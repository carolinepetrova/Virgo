<?php
$to      = 'support@virgoapp.eu';
$subject = strip_tags(htmlspecialchars($_POST['name']));
$message = strip_tags(htmlspecialchars($_POST['text']));
$from = strip_tags(htmlspecialchars($_POST['email']));
$headers = 'From:'.$from. "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
return true;         
?>
