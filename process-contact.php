<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);

    // Validate input
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    if (empty($message)) {
        $errors[] = "Message is required";
    }

    if (empty($errors)) {
        try {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);

            // Send email notification to admin
            $to = "admin@freelancemarketplace.com";
            $email_subject = "New Contact Form Submission: " . $subject;
            $email_body = "Name: " . $name . "\n";
            $email_body .= "Email: " . $email . "\n";
            $email_body .= "Subject: " . $subject . "\n";
            $email_body .= "Message: " . $message . "\n";
            $headers = "From: " . $email . "\r\n";
            $headers .= "Reply-To: " . $email . "\r\n";

            mail($to, $email_subject, $email_body, $headers);

            // Set success message
            $_SESSION['success_message'] = "Thank you for your message. We will get back to you soon!";
            header("Location: contact.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "An error occurred. Please try again later.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ];
        header("Location: contact.php");
        exit();
    }
} else {
    header("Location: contact.php");
    exit();
} 