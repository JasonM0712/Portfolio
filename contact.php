<?php
session_start();
 
header('Content-Type: application/json');
 
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    echo json_encode(["csrf_token" => $_SESSION["csrf_token"]]);
    exit;
}
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    if (
        empty($_POST["csrf_token"]) ||
        !isset($_SESSION["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        echo json_encode(["error" => "Requête invalide."]);
        exit;
    }
 
    $name    = strip_tags(trim($_POST["name"]    ?? ""));
    $email   = filter_var(trim($_POST["email"]   ?? ""), FILTER_SANITIZE_EMAIL);
    $objet   = strip_tags(trim($_POST["objet"]   ?? ""));
    $message = strip_tags(trim($_POST["message"] ?? ""));
 
    if (empty($name) || empty($email) || empty($objet) || empty($message)) {
        http_response_code(400);
        echo json_encode(["error" => "Tous les champs sont requis."]);
        exit;
    }
 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Adresse email invalide."]);
        exit;
    }
 
    if (strlen($name) > 100 || strlen($objet) > 200 || strlen($message) > 5000) {
        http_response_code(400);
        echo json_encode(["error" => "Un champ dépasse la longueur autorisée."]);
        exit;
    }
 
    $to = "jason.morel@my-digital-school.org";
 
    // Sujet encodé en UTF-8 (RFC 2047) pour que les accents passent
    $subject = "=?UTF-8?B?" . base64_encode("Message depuis portfolio : $objet") . "?=";
 
    $body  = "Nom: $name\r\n";
    $body .= "Email: $email\r\n\r\n";
    $body .= "Message:\r\n$message";
 
    // En-têtes avec charset UTF-8 déclaré
    $headers  = "From: Portfolio <noreply@tondomaine.fr>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
 
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
 
    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["success" => "Message envoyé !"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de l'envoi."]);
    }
}
?>