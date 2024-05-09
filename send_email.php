<?php
    $data = json_decode(file_get_contents("php://input"));
    $email = $data->email;

    // Send email
    $subject = "Welcome to Our Site";
    $body = "Dear User,\n\nThank you for logging in to our site. We're excited to have you on board!\n\nBest regards,\nThe Site Team";
    $to = $email;
    $headers = "From: elton.balla@iticopernico.it" . "\r\n" . // Change to your email address
               "Reply-To: elton.balla@iticopernico.it" . "\r\n" . // Change to your email address
               "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        echo "Email sent successfully!";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Failed to send email. Please try again later.";
    }
?>



