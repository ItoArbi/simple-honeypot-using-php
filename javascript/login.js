const form = document.querySelector(".login form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

// Track user interaction timing
let lastInteraction = Date.now();

// Update last interaction on mouse movement or key press
document.addEventListener('mousemove', () => lastInteraction = Date.now());
document.addEventListener('keydown', () => lastInteraction = Date.now());

form.onsubmit = (e)=>{
    e.preventDefault();
}

continueBtn.onclick = ()=>{
    // Check if CAPTCHA is required and validate it
    if (isCaptchaRequired() && !validateCaptcha()) {
        errorText.style.display = "block";
        errorText.textContent = "Please complete the CAPTCHA verification";
        return;
    }

    // Check for bot-like behavior (fast form filling)
    if (detectBotBehavior()) {
        redirectToHoneypot();
        return;
    }
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/login.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data === "success"){
                location.href = "users.php";
              }else{
                errorText.style.display = "block";
                errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}

// Check if CAPTCHA is required (brute force protection active)
function isCaptchaRequired() {
    return document.querySelector('.g-recaptcha') !== null;
}

// Validate reCAPTCHA
function validateCaptcha() {
    const response = grecaptcha.getResponse();
    return response.length > 0;
}

// Bot detection functions
function detectBotBehavior() {
    // Check if fields were filled too quickly (less than 100ms between interactions)
    const timeSinceLastInteraction = Date.now() - lastInteraction;
    if (timeSinceLastInteraction < 100) {
        console.log("Bot detected: Unnaturally fast form filling");
        return true;
    }
    
    // Check if form was submitted too quickly after page load
    const pageLoadTime = Date.now() - window.performance.timing.navigationStart;
    if (pageLoadTime < 1500) { // Less than 1.5 seconds
        console.log("Bot detected: Form submitted too quickly after page load");
        return true;
    }
    
    // Check for automation tools
    if (navigator.webdriver || window.__webdriver_evaluate || window.__selenium_evaluate) {
        console.log("Bot detected: WebDriver detected");
        return true;
    }
    
    return false;
}

function redirectToHoneypot() {
    // Create a hidden form to submit to the honeypot redirect handler
    const hiddenForm = document.createElement('form');
    hiddenForm.method = 'POST';
    hiddenForm.action = 'php/honeypot_redirect.php';
    hiddenForm.style.display = 'none';
    
    // Add all form data to maintain appearance of normal submission
    const originalFormData = new FormData(form);
    for (let [key, value] of originalFormData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        hiddenForm.appendChild(input);
    }
    
    // Add detection reason
    const detectionInput = document.createElement('input');
    detectionInput.type = 'hidden';
    detectionInput.name = 'detection_reason';
    detectionInput.value = 'javascript_behavior';
    hiddenForm.appendChild(detectionInput);
    
    document.body.appendChild(hiddenForm);
    hiddenForm.submit();
}