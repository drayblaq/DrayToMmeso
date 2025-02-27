<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Handle file upload if any
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $file_type = $_FILES['file']['type'];
        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp_path)));
        
        $boundary = md5(time());
        
        // Define recipient email
        $to = "alabidamilare98@gmail.com"; // change to your email
        
        // Email headers
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        // Email body with attachment
        $emailContent = "--$boundary\r\n";
        $emailContent .= "Content-Type: text/html; charset=UTF-8\r\n";
        $emailContent .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $emailContent .= "<h2>New Contact Message from $name</h2><p><strong>Email:</strong> $email</p><p><strong>Subject:</strong> $subject</p><p><strong>Message:</strong><br>$message</p>\r\n";
        $emailContent .= "--$boundary\r\n";
        $emailContent .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $emailContent .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $emailContent .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $emailContent .= "$file_content\r\n";
        $emailContent .= "--$boundary--\r\n";
        
        // Send the email
        if (mail($to, $subject, $emailContent, $headers)) {
            echo "Message sent successfully with the attachment!";
        } else {
            echo "Failed to send the message with the attachment. Please try again.";
        }
    } else {
        echo "No file uploaded.";
    }
}
?>
