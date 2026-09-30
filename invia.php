<?php
// 1. Carica l'ambiente di WordPress per poter usare le sue funzioni
// Cerca il file wp-load.php risalendo le cartelle
$wp_load_path = __DIR__;
for ($i = 0; $i < 5; $i++) {
    if (file_exists($wp_load_path . '/wp-load.php')) {
        require_once($wp_load_path . '/wp-load.php');
        break;
    }
    $wp_load_path = dirname($wp_load_path);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Raccoglie e pulisce i dati inviati dal form
    $nome     = htmlspecialchars(trim($_POST['nome'] ?? ''));
    $email    = sanitize_email(trim($_POST['email'] ?? ''));
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $ospiti   = htmlspecialchars(trim($_POST['ospiti'] ?? ''));
    $data     = htmlspecialchars(trim($_POST['data'] ?? ''));
    $orario   = htmlspecialchars(trim($_POST['orario'] ?? ''));

    // --- CONFIGURAZIONE EMAIL ---
    $email_ristorante = "settedue4@gmail.com"; 
    $nome_ristorante  = "Ristorante Pizzeria Setteduequattro";

    // Imposta le email in formato HTML
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // 3. EMAIL AL RISTORANTE (Notifica nuova prenotazione)
    $to_admin      = $email_ristorante;
    $subject_admin = "Nuova Prenotazione Tavolo - " . $nome;
    $headers_admin = $headers;
    if (!empty($email)) {
        $headers_admin[] = 'Reply-To: ' . $nome . ' <' . $email . '>';
    }

    $body_admin = "
    <h2>Nuova richiesta di prenotazione</h2>
    <p><strong>Cliente:</strong> {$nome}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Telefono:</strong> {$telefono}</p>
    <p><strong>Numero Ospiti:</strong> {$ospiti}</p>
    <p><strong>Data:</strong> {$data}</p>
    <p><strong>Orario:</strong> {$orario}</p>
    ";

    // Invio tramite wp_mail()
    wp_mail($to_admin, $subject_admin, $body_admin, $headers_admin);

    // 4. EMAIL AL CLIENTE (Conferma di ricezione)
    if (!empty($email) && is_email($email)) {
        $to_client      = $email;
        $subject_client = "Conferma Prenotazione - " . $nome_ristorante;
        $headers_client = $headers;
        $headers_client[] = 'Reply-To: ' . $nome_ristorante . ' <' . $email_ristorante . '>';

        $body_client = "
        <h2>Grazie per la tua prenotazione, {$nome}!</h2>
        <p>Abbiamo ricevuto la tua richiesta per il giorno <strong>{$data}</strong> alle ore <strong>{$orario}</strong> per <strong>{$ospiti}</strong>.</p>
        <p>Ti aspettiamo in Via Roma 724, Ispra (VA).</p>
        <p><em>Per qualsiasi modifica o disdetta, puoi contattarci al numero +39 0332 780155.</em></p>
        <hr>
        <p><strong>{$nome_ristorante}</strong></p>
        ";

        wp_mail($to_client, $subject_client, $body_client, $headers_client);
    }

    // Risposta per la fetch JavaScript
    echo "OK";
} else {
    header("HTTP/1.1 403 Forbidden");
    echo "Accesso non consentito.";
}
?>