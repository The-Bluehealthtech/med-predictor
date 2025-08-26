<?php
/**
 * Test SMTP Simple pour Diagnostic
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

echo "🧪 Test SMTP Simple - Diagnostic\n";
echo "================================\n\n";

// Configuration
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_username = 'im0668@gmail.com';
$smtp_password = 'qzneatcozirhlpst';

echo "📋 Configuration:\n";
echo "Host: $smtp_host\n";
echo "Port: $smtp_port\n";
echo "Username: $smtp_username\n";
echo "Password: " . substr($smtp_password, 0, 4) . "****\n\n";

try {
    // Créer une instance PHPMailer
    $mail = new PHPMailer(true);
    
    // Activer le debug SMTP
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    // Configuration du serveur
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;
    
    // Configuration de l'email
    $mail->setFrom($smtp_username, 'Test CI/CD');
    $mail->addAddress($smtp_username, 'Test Recipient');
    
    // Sujet et contenu simple
    $mail->Subject = 'Test SMTP Simple';
    $mail->Body = 'Ceci est un test simple de SMTP.';
    
    echo "📤 Tentative d'envoi...\n";
    $mail->send();
    
    echo "✅ Email envoyé avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    
    // Informations détaillées sur l'erreur
    if (strpos($e->getMessage(), 'Could not authenticate') !== false) {
        echo "\n🔍 Diagnostic de l'erreur d'authentification:\n";
        echo "1. Vérifiez que l'authentification 2FA est activée\n";
        echo "2. Vérifiez que le mot de passe d'application est correct\n";
        echo "3. Vérifiez que l'email est correct\n";
        echo "4. Essayez de générer un nouveau mot de passe d'application\n";
    }
}
?>
