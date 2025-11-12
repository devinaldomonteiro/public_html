<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // Validação básica
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "error" => "Dados inválidos"]);
        exit;
    }

    // Configurações do e-mail
    $to = "inaldomonteiroti@gmail.com"; // <-- coloque o seu e-mail aqui
    $subject = "Nova mensagem de contato de $name";
    $body = "Nome: $name\nEmail: $email\n\nMensagem:\n$message";
    $headers = "From: $name <$email>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";

    // Enviar o e-mail
    $success = mail($to, $subject, $body, $headers);

    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
