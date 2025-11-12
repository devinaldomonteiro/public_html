<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json; charset=UTF-8");

require 'vendor/autoload.php'; // se estiver usando Composer
// ou: require 'PHPMailer/src/PHPMailer.php'; require 'PHPMailer/src/SMTP.php'; require 'PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(["success" => false, "error" => "Preencha todos os campos."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => "Email inválido."]);
        exit;
    }

    // === CONFIGURAÇÃO DO EMAIL ===
    $mail = new PHPMailer(true);
    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';      // servidor SMTP do Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'seuemail@gmail.com'; // seu e-mail
        $mail->Password = 'Deuseamor1981';      // senha de app do Gmail (não a senha normal!)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom($email, $name);
        $mail->addAddress('inaldomonteiroti@gmail.com', 'Site Contato'); // para onde será enviado

        // Conteúdo
        $mail->isHTML(false);
        $mail->Subject = "📩 Nova mensagem de $name";
        $mail->Body = "Nome: $name\nEmail: $email\n\nMensagem:\n$message";

        // Envia
        $mail->send();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido."]);
}