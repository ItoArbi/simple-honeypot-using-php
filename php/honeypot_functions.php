<?php

// Improved IP detection
function get_client_ip() {
    $ip_keys = [
        'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
        'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'
    ];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip_list = explode(',', $_SERVER[$key]);
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
}

// Enhanced SQL injection detection
function detect_sql_injection($input) {
    if (!is_string($input)) return false;
    
    $patterns = [
        // Basic patterns
        '/\b(union|select|insert|update|delete|drop|truncate|alter|create|exec)\b/i',
        '/\b(or|and)\s+[\d\'\"]\s*=\s*[\d\'\"]/i',
        '/--|\/\*|\*\//',
        
        // Advanced evasion techniques
        '/\b(where|like|having)\s+[\w\'\"]\s*[=<>]/i',
        '/\b(sleep|benchmark|waitfor|delay)\s*\(/i',
        '/[\'"]\s*[+|\|\|]\s*[\'"]/',
        '/\b(version|database|schema|table|column)\b/i',
        '/\b(if|nullif|coalesce)\s*\(/i',
        '/\b(load_file|outfile|dumpfile)\s*\(/i',
        '/\b(hex|unhex|char)\s*\(/i',
        '/\b(procedure|analyse)\s*\(/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    return false;
}

// Enhanced XSS detection
function detect_xss($input) {
    if (!is_string($input)) return false;
    
    $patterns = [
        '/<script|javascript:|onerror=|onload=|onmouse=|eval\(|alert\(/i',
        '/<\w+[^>]*\s(on\w+|style)=/i',
        '/expression\s*\(|url\s*\(/i',
        '/data:\s*(text|image)\/(html|svg)/i',
        '/<\w+:\w+|\w+=\\\\?["\']\s*\/?>/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    return false;
}

// Improved brute force protection
function check_brute_force($ip) {
    $brute_force_file = 'brute_force_attempts.json';
    $max_attempts = 5;
    $lockout_time = 900; // 15 minutes
    
    // Initialize if not exists
    if (!file_exists($brute_force_file)) {
        file_put_contents($brute_force_file, json_encode([]));
    }
    
    $attempts = json_decode(file_get_contents($brute_force_file), true) ?: [];
    $current_time = time();
    
    // Filter recent attempts
    $attempts[$ip] = array_filter($attempts[$ip] ?? [], function($t) use ($current_time, $lockout_time) {
        return ($current_time - $t) < $lockout_time;
    });
    
    // Log new attempt
    $attempts[$ip][] = $current_time;
    file_put_contents($brute_force_file, json_encode($attempts));
    
    return count($attempts[$ip]) >= $max_attempts;
}

// Enhanced attack logging
function log_attack($ip, $technique, $details = "") {
    $log_entry = sprintf(
        "[%s] %s - IP: %s - Details: %s\n",
        date('Y-m-d H:i:s'),
        $technique,
        $ip,
        substr($details, 0, 2000) // Limit length
    );
    
    // Append to log file
    file_put_contents('honeypot_attacks.log', $log_entry, FILE_APPEND);
    
    // Send Telegram alert
    send_telegram_alert($technique, $ip, $details);
}

// Enhanced Telegram alert
function send_telegram_alert($technique, $ip, $details) {
    $botToken = '7692823493:AAFypNZT7Wszhb6buUr4IwzU9fRCJW0e2sU';
    $chatId = '1990047821';
    
    $message = sprintf(
        "<b>🚨 Honeypot Alert</b>\n<b>Type:</b> %s\n<b>IP:</b> %s\n<b>Details:</b> %s",
        htmlspecialchars($technique),
        htmlspecialchars($ip),
        htmlspecialchars(substr($details, 0, 1000))
    );
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage?" . http_build_query([
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);
    
    @file_get_contents($url);
}

// Advanced suspicious request detection
function is_suspicious_request($ip) {
    // Check for brute force
    if (check_brute_force($ip)) {
        return true;
    }
    
    // Check for SQL injection in all input data
    foreach ($_REQUEST as $value) {
        if (is_array($value)) continue;
        if (detect_sql_injection($value)) {
            return true;
        }
    }
    
    // Check for XSS attempts
    foreach ($_REQUEST as $value) {
        if (is_array($value)) continue;
        if (detect_xss($value)) {
            return true;
        }
    }
    
    // Check for known bad user agents
    $bad_user_agents = [
        'sqlmap', 'nikto', 'wget', 'curl', 'hydra', 'zap', 'burp',
        'nmap', 'metasploit', 'dirbuster', 'gobuster', 'arachni'
    ];
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    foreach ($bad_user_agents as $bad_ua) {
        if (stripos($user_agent, $bad_ua) !== false) {
            return true;
        }
    }
    
    // Check for headless browsers
    if (preg_match('/HeadlessChrome|PhantomJS|Puppeteer/i', $user_agent)) {
        return true;
    }
    
    // Check for filled honeypot field
    if (!empty($_POST['honeypot'])) {
        return true;
    }
    
    return false;
}

// Fake database response generator
function generate_fake_response() {
    $responses = [
        "success",
        "Invalid credentials",
        "Account locked - contact support",
        "Two-factor authentication required",
        "Session expired"
    ];
    return $responses[array_rand($responses)];
}
?>