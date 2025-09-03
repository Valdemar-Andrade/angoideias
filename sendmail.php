<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
   // Captura os dados e faz limpeza básica
    $name    = trim($_POST["name"] ?? '');
    $email   = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');

    // Validações
    if (empty($name) || empty($email) || empty($message)) {
        die("<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.history.back();</script>");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('E-mail inválido.'); window.history.back();</script>");
    }

    // Previne injeção de código
    $name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP (Hostinger)
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'adilma.andrade@angoideias.com'; // e-mail da empresa (Hostinger)
        $mail->Password   = 'A.andrade21';         // senha definida no painel Hostinger
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Remetente (fixo = corporativo da empresa)
        $mail->setFrom('adilma.andrade@angoideias.com', 'Website AngoIdeias');

        // Define o cliente como Reply-To (assim empresa responde direto ao cliente)
        $mail->addReplyTo($email, $name);

        // Destinatários (3 preferidos da empresa)
        $mail->addAddress('adilma.andrade@angoideias.com');
        $mail->addAddress('info@angoideias.com');
        $mail->addAddress('ahmed.mmb@angoideias.com');

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = '📩 Novo contato via site AngoIdeias';
        $mail->Body    = "
            <h3>Novo contato recebido pelo site</h3>
            <p><b>Nome:</b> {$name}</p>
            <p><b>E-mail:</b> {$email}</p>
            <p><b>Mensagem:</b><br>" . nl2br($message) . "</p>
            <hr>
            <small>Este e-mail foi enviado automaticamente através do formulário do site AngoIdeias.</small>
        ";
        $mail->AltBody = "Novo contato recebido pelo site\n\nNome: {$name}\nE-mail: {$email}\n\nMensagem:\n{$message}";

        // Envia
        $mail->send();
        echo "<script>alert('Mensagem enviada com sucesso!'); window.location.href='index.html';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Erro ao enviar: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
}
?>