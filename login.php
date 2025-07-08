<?php 
session_start();
if(isset($_SESSION['unique_id'])){
    header("location: users.php");
}

// Include detection functions
require_once 'php/honeypot_functions.php';

$client_ip = get_client_ip();


// Check for suspicious activity
if (is_suspicious_request($client_ip)) {
  // Log the attempt
  log_attack($client_ip, "Suspicious Activity Detected", json_encode($_REQUEST));
  
  // Set honeypot mode without redirect
  $_SESSION['honeypot_mode'] = true;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (check_brute_force(get_client_ip())) {
        if (empty($_POST['g-recaptcha-response'])) {
            echo "Please complete the CAPTCHA verification";
            exit();
        }
        
        $secretKey = "6LcsbncrAAAAAPUEyAWsBYpRhKe4rSkhNXqsnyUz";
        $response = $_POST['g-recaptcha-response'];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}");
        $captchaSuccess = json_decode($verify);
        
        if (!$captchaSuccess->success) {
            echo "CAPTCHA verification failed. Please try again.";
            exit();
        }
    }
    // If in honeypot mode, process differently
    if ($_SESSION['honeypot_mode'] ?? false) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Log the attempt
        log_attack($client_ip, "Honeypot Login Attempt", "Email: $email");
        
        // Fake processing delay
        usleep(rand(500000, 1500000)); // 0.5-1.5 second delay
        
        // Return fake response
        echo generate_fake_response();
        exit();
    }
    
    // Check for brute force and validate CAPTCHA if needed
    if (check_brute_force(get_client_ip())) {
        if (empty($_POST['g-recaptcha-response'])) {
            header("location: login.php?error=Please complete the CAPTCHA verification");
            exit();
        }
        
        $secretKey = "6LcsbncrAAAAAPUEyAWsBYpRhKe4rSkhNXqsnyUz"; // Add your reCAPTCHA secret key
        $response = $_POST['g-recaptcha-response'];
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}");
        $captchaSuccess = json_decode($verify);
        
        if (!$captchaSuccess->success) {
            header("location: login.php?error=CAPTCHA verification failed. Please try again.");
            exit();
        }
    }
}
?>

<?php include_once "header.php"; ?>

<body>
  <div class="wrapper">
    <section class="form login">
      <header>FamKom Chat App</header>
      <form action="login.php" method="POST" autocomplete="off">
        <div class="error-text"><?php 
            // Show brute force warning if applicable
            if (check_brute_force(get_client_ip())) {
                echo "Too many login attempts. Please complete the CAPTCHA verification.";
            } elseif (isset($_GET['error'])) {
                echo htmlspecialchars($_GET['error']);
            }
        ?></div>
        
        <!-- Hidden honeypot field -->
        <input type="text" name="honeypot" style="display:none;" tabindex="-1">
        
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-eye"></i>
        </div>
        
        <!-- Conditional CAPTCHA -->
        <?php if (check_brute_force(get_client_ip())): ?>
        <div class="field captcha">
          <label>Verify you're human</label>
          <div class="g-recaptcha" data-sitekey="6LcsbncrAAAAAPUEyAWsBYpRhKe4rSkhNXqsnyUz" required></div>
        </div>
        <?php endif; ?>
        
        <div class="field button">
          <input type="submit" name="submit" value="Continue to Chat">
        </div>
      </form>
      <div class="link">Not yet signed up? <a href="index.php">Signup now</a></div>
    </section>
  </div>
  
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  
  <script>
    // Client-side validation for required CAPTCHA
    document.querySelector('form').addEventListener('submit', function(e) {
        <?php if (check_brute_force(get_client_ip())): ?>
            if (grecaptcha && grecaptcha.getResponse().length === 0) {
                e.preventDefault();
                alert("Please complete the CAPTCHA verification");
                return false;
            }
        <?php endif; ?>
    });
  </script>
</body>
</html>