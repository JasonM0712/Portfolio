<?php
session_start();

header('Content-Type: application/json');

// Appel GET : initialise et retourne le token CSRF
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    echo json_encode(["csrf_token" => $_SESSION["csrf_token"]]);
    exit;
}

// Appel POST : traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Vérification CSRF
    if (
        empty($_POST["csrf_token"]) ||
        !isset($_SESSION["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        echo json_encode(["error" => "Requête invalide."]);
        exit;
    }

    // Nettoyage des entrées (protection XSS)
    $name    = strip_tags(trim($_POST["name"]    ?? ""));
    $email   = filter_var(trim($_POST["email"]   ?? ""), FILTER_SANITIZE_EMAIL);
    $objet   = strip_tags(trim($_POST["objet"]   ?? ""));
    $message = strip_tags(trim($_POST["message"] ?? ""));

    // Champs vides
    if (empty($name) || empty($email) || empty($objet) || empty($message)) {
        http_response_code(400);
        echo json_encode(["error" => "Tous les champs sont requis."]);
        exit;
    }

    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Adresse email invalide."]);
        exit;
    }

    // Validation longueur
    if (strlen($name) > 100 || strlen($objet) > 200 || strlen($message) > 5000) {
        http_response_code(400);
        echo json_encode(["error" => "Un champ dépasse la longueur autorisée."]);
        exit;
    }

    // Envoi du mail
    $to      = "jason.morel@my-digital-school.org";
    $subject = "Message depuis portfolio : $objet";

    $body  = "Nom: $name\n";
    $body .= "Email: $email\n\n";
    $body .= "Message:\n$message";

    $headers  = "From: Portfolio <noreply@tondomaine.fr>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Régénération du token après envoi
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["success" => "Message envoyé !"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Erreur lors de l'envoi."]);
    }
}
?>