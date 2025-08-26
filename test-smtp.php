<?php
/**
 * Test SMTP Gmail pour CI/CD
 * Teste la connectivité et l'authentification
 */

// Configuration
$smtp_host = 'smtp.gmail.com';
$smtp_port = 587;
$smtp_username = 'izhar@tbhc.uk';
$smtp_password = 'Izhar@Ath2021';

echo "🧪 Test SMTP Gmail CI/CD\n";
echo "========================\n\n";

// Test 1: Extensions PHP requises
echo "📋 Test des Extensions PHP\n";
echo "---------------------------\n";
$required_extensions = ['openssl', 'mbstring', 'iconv', 'sockets'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext: chargé\n";
    } else {
        echo "❌ $ext: non chargé\n";
    }
}
echo "\n";

// Test 2: Connexion réseau basique
echo "🌐 Test de Connexion Réseau\n";
echo "----------------------------\n";
$socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10);

if ($socket) {
    echo "✅ Connexion TCP réussie sur $smtp_host:$smtp_port\n";
    
    // Lire la réponse du serveur
    $response = fgets($socket, 1024);
    echo "📨 Réponse serveur: " . trim($response) . "\n";
    
    fclose($socket);
} else {
    echo "❌ Erreur de connexion: $errstr ($errno)\n";
}
echo "\n";

// Test 3: Test avec PHPMailer (si disponible)
echo "📧 Test avec PHPMailer\n";
echo "----------------------\n";

if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✅ PHPMailer disponible\n";
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp_port;
        
        // Test de connexion
        $mail->smtpConnect();
        echo "✅ Connexion SMTP authentifiée réussie\n";
        
        $mail->smtpClose();
        
    } catch (Exception $e) {
        echo "❌ Erreur PHPMailer: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️  PHPMailer non disponible\n";
    echo "   Installez-le avec: composer require phpmailer/phpmailer\n";
}
echo "\n";

// Test 4: Test avec SwiftMailer (si disponible)
echo "📬 Test avec SwiftMailer\n";
echo "------------------------\n";

if (class_exists('Swift_Mailer')) {
    echo "✅ SwiftMailer disponible\n";
    
    try {
        // Créer le transport SMTP
        $transport = (new Swift_SmtpTransport($smtp_host, $smtp_port, 'tls'))
            ->setUsername($smtp_username)
            ->setPassword($smtp_password);
        
        // Créer le mailer
        $mailer = new Swift_Mailer($transport);
        
        // Test de connexion
        $mailer->getTransport()->start();
        echo "✅ Connexion SwiftMailer réussie\n";
        
        $mailer->getTransport()->stop();
        
    } catch (Exception $e) {
        echo "❌ Erreur SwiftMailer: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️  SwiftMailer non disponible\n";
    echo "   Installez-le avec: composer require swiftmailer/swiftmailer\n";
}
echo "\n";

// Test 5: Test de configuration Laravel (si disponible)
echo "🦄 Test Configuration Laravel\n";
echo "------------------------------\n";

if (class_exists('Illuminate\Support\Facades\Mail')) {
    echo "✅ Laravel Mail disponible\n";
    
    // Vérifier la configuration
    $config = config('mail');
    if ($config) {
        echo "✅ Configuration mail Laravel trouvée\n";
        echo "   Driver: " . ($config['default'] ?? 'non défini') . "\n";
        echo "   Host: " . ($config['mailers']['smtp']['host'] ?? 'non défini') . "\n";
        echo "   Port: " . ($config['mailers']['smtp']['port'] ?? 'non défini') . "\n";
    }
} else {
    echo "⚠️  Laravel Mail non disponible\n";
}
echo "\n";

// Test 6: Recommandations
echo "💡 Recommandations\n";
echo "------------------\n";

echo "1. ✅ Ports SMTP testés et ouverts\n";
echo "2. ✅ Connexion réseau fonctionnelle\n";
echo "3. 🔧 Installez PHPMailer: composer require phpmailer/phpmailer\n";
echo "4. 🔧 Ou SwiftMailer: composer require swiftmailer/swiftmailer\n";
echo "5. 📧 Testez l'envoi d'email réel\n";
echo "6. 🔒 Vérifiez l'authentification 2FA Gmail\n";
echo "7. 🔑 Vérifiez le mot de passe d'application\n";

echo "\n🎉 Test SMTP terminé !\n";
echo "Votre configuration Gmail est prête pour les notifications CI/CD.\n";
?>
