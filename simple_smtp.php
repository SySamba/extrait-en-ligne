<?php
/**
 * Simple SMTP Client pour l'envoi d'emails
 * Version simplifiée sans dépendances externes
 * Mairie de Khombole
 */

class SimpleSMTP {
    private $host;
    private $port;
    private $username;
    private $password;
    private $encryption;
    private $socket;
    
    public function __construct($host, $port, $username, $password, $encryption = 'tls') {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->encryption = $encryption;
    }
    
    /**
     * Envoie un email via SMTP
     */
    public function sendEmail($from, $fromName, $to, $subject, $body, $isHtml = true) {
        try {
            // Connexion au serveur SMTP
            if (!$this->connect()) {
                return false;
            }
            
            // Authentification
            if (!$this->authenticate()) {
                $this->disconnect();
                return false;
            }
            
            // Envoi de l'email
            $result = $this->sendMessage($from, $fromName, $to, $subject, $body, $isHtml);
            
            // Déconnexion
            $this->disconnect();
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Erreur SimpleSMTP: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Connexion au serveur SMTP
     */
    private function connect() {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        if ($this->encryption === 'ssl') {
            $this->socket = @stream_socket_client("ssl://{$this->host}:{$this->port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        } else {
            $this->socket = @stream_socket_client("{$this->host}:{$this->port}", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        }
        
        if (!$this->socket) {
            error_log("Impossible de se connecter à {$this->host}:{$this->port} - $errstr ($errno)");
            return false;
        }
        
        // Lire la réponse de bienvenue
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '220') {
            error_log("Réponse SMTP inattendue: $response");
            return false;
        }
        
        // EHLO
        $this->sendCommand("EHLO {$this->host}");
        $response = $this->readResponse();
        
        // STARTTLS si nécessaire
        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS");
            $response = $this->readResponse();
            if (substr($response, 0, 3) !== '220') {
                error_log("STARTTLS échoué: $response");
                return false;
            }
            
            // Activer TLS
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("Impossible d'activer TLS");
                return false;
            }
            
            // Nouveau EHLO après TLS
            $this->sendCommand("EHLO {$this->host}");
            $response = $this->readResponse();
        }
        
        return true;
    }
    
    /**
     * Authentification SMTP
     */
    private function authenticate() {
        $this->sendCommand("AUTH LOGIN");
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '334') {
            error_log("AUTH LOGIN échoué: $response");
            return false;
        }
        
        // Username
        $this->sendCommand(base64_encode($this->username));
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '334') {
            error_log("Username échoué: $response");
            return false;
        }
        
        // Password
        $this->sendCommand(base64_encode($this->password));
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '235') {
            error_log("Password échoué: $response");
            return false;
        }
        
        return true;
    }
    
    /**
     * Envoi du message
     */
    private function sendMessage($from, $fromName, $to, $subject, $body, $isHtml) {
        // MAIL FROM
        $this->sendCommand("MAIL FROM:<$from>");
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            error_log("MAIL FROM échoué: $response");
            return false;
        }
        
        // RCPT TO
        $this->sendCommand("RCPT TO:<$to>");
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            error_log("RCPT TO échoué: $response");
            return false;
        }
        
        // DATA
        $this->sendCommand("DATA");
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '354') {
            error_log("DATA échoué: $response");
            return false;
        }
        
        // Headers et corps du message
        $headers = "From: $fromName <$from>\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";
        $headers .= "\r\n";
        
        $message = $headers . $body . "\r\n.";
        
        fwrite($this->socket, $message . "\r\n");
        $response = $this->readResponse();
        if (substr($response, 0, 3) !== '250') {
            error_log("Envoi message échoué: $response");
            return false;
        }
        
        return true;
    }
    
    /**
     * Envoie une commande SMTP
     */
    private function sendCommand($command) {
        fwrite($this->socket, $command . "\r\n");
    }
    
    /**
     * Lit la réponse du serveur
     */
    private function readResponse() {
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return trim($response);
    }
    
    /**
     * Déconnexion
     */
    private function disconnect() {
        if ($this->socket) {
            $this->sendCommand("QUIT");
            fclose($this->socket);
            $this->socket = null;
        }
    }
}
?>
