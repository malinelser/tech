<?php
header('Content-Type: application/json');

// Set recipient email
$to = "malin.elser@gmail.com";

// Collect and sanitize form data
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate inputs
if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Please complete the form correctly.']);
    exit;
}

// Email setup
$subject = "New contact from $name";
$email_content = "Name: $name\n";
$email_content .= "Email: $email\n\n";
$email_content .= "Message:\n$message\n";

$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Attempt to send email
try {
    $mailSent = mail($to, $subject, $email_content, $headers);
    
    if ($mailSent) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Thank you! Your message has been sent.']);
    } else {
        // Get more detailed error information
        $error = error_get_last();
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Mail function failed.', 
            'error' => $error['message'] ?? 'Unknown error'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'An exception occurred: ' . $e->getMessage()]);
}
?>