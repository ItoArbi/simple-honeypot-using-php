<?php 
// Enhanced honeypot version of login.php
session_start();

// Telegram alert function
function send_telegram_alert($message) {
    $botToken = '7692823493:AAFypNZT7Wszhb6buUr4IwzU9fRCJW0e2sU';
    $chatId = '1990047821';
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

// Log attack details
function log_attack($ip, $technique, $details = "") {
    $log_entry = sprintf(
        "[%s] Attack detected - IP: %s - Technique: %s - Details: %s\n",
        date('Y-m-d H:i:s'),
        $ip,
        $technique,
        $details
    );
    
    file_put_contents('honeypot_attacks.log', $log_entry, FILE_APPEND);
    
    // Send Telegram alert
    $telegram_msg = "<b>🚨 Honeypot Alert 🚨</b>\n";
    $telegram_msg .= "<b>IP:</b> $ip\n";
    $telegram_msg .= "<b>Technique:</b> $technique\n";
    $telegram_msg .= "<b>Details:</b> " . substr($details, 0, 1000) . "\n";
    
    send_telegram_alert($telegram_msg);
}

// Check for SQL injection attempts
function detect_sql_injection($input) {
    // Check for SQL-like patterns
    $patterns = [
        '/\b(union|select|insert|update|delete|drop|exec)\b/i',
        '/\b(or|and)\s+[\d\'\"]/i',
        '/--|\/\*|\*\//', // SQL comments
        '/\b(where|like)\s+[\w\'\"]\s*=/i',
        '/\bsleep\(\s*\d+\s*\)|\bwaitfor\s+delay/i',
        '/[\'"]\s*\+\s*[\'"]/i', // String concatenation
        '/\b(information_schema|pg_catalog)\b/i',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    return false;
}

// Check for brute force attempts
function check_brute_force($ip) {
    $brute_force_file = 'brute_force_attempts.txt';
    $max_attempts = 5;
    $lockout_time = 300; // 5 minutes
    
    $attempts = [];
    if (file_exists($brute_force_file)) {
        $attempts = json_decode(file_get_contents($brute_force_file), true);
    }
    
    $current_time = time();
    $attempts[$ip] = array_filter($attempts[$ip] ?? [], function($t) use ($current_time, $lockout_time) {
        return ($current_time - $t) < $lockout_time;
    });
    
    $attempts[$ip][] = $current_time;
    file_put_contents($brute_force_file, json_encode($attempts));
    
    return count($attempts[$ip]) >= $max_attempts;
}

// Get client IP
function get_client_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip_list = explode(',', $_SERVER[$key]);
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

$client_ip = get_client_ip();

// Check if this is a brute force attempt
if (check_brute_force($client_ip)) {
    log_attack($client_ip, "Brute Force Attempt", "Too many login attempts");
    header("HTTP/1.0 429 Too Many Requests");
    die("Too many login attempts. Please try again later.");
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check for SQL injection
    if (detect_sql_injection($email) || detect_sql_injection($password)) {
        log_attack($client_ip, "SQL Injection Attempt", "Email: $email, Password: $password");
        // Fake successful response to keep attacker engaged
        echo "success";
        exit();
    }
    
    // Check for XSS attempts
    if (preg_match('/<script|javascript|onerror|onload/i', $email.$password)) {
        log_attack($client_ip, "XSS Attempt", "Email: $email, Password: $password");
        echo "success"; // Fake response
        exit();
    }
    
    // Log all attempts (legitimate or not)
    log_attack($client_ip, "Login Attempt", "Email: $email, Password: $password");
    
    // Always respond with "success" to keep attackers engaged
    echo "success";
    exit();
}

if (!isset($_SESSION['honeypot_mode'])) {
    $client_ip = get_client_ip();
    
    if (is_suspicious_request($client_ip)) {
        $_SESSION['honeypot_mode'] = true;
    } else {
        // Not suspicious - redirect back to real login
        header("Location: login.php");
        exit();
    }
}
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form login">
      <header>FamKom Chat App</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-eye"></i>
        </div>
        <!-- Hidden honeypot field -->
        <input type="text" name="honeypot" style="display:none;">
        <div class="field button">
          <input type="submit" name="submit" value="Continue to talk">
        </div>
      </form>
      <div class="link">Not yet signed up? <a href="index.php">Signup now</a></div>
    </section>
  </div>
  
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/honeypot_login.js"></script>

</body>
</html>