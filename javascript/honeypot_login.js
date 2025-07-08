const form = document.querySelector(".login form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

// Enhanced honeypot detection
const honeypotField = form.querySelector("input[name='honeypot']");
let interactionTimes = [];
const pageLoadTime = Date.now();

// Track user interactions
document.addEventListener('mousemove', trackInteraction);
document.addEventListener('keydown', trackInteraction);
document.addEventListener('click', trackInteraction);

function trackInteraction() {
    interactionTimes.push(Date.now());
    if (interactionTimes.length > 10) {
        interactionTimes.shift();
    }
}

form.onsubmit = (e) => e.preventDefault();

continueBtn.onclick = () => {
    // Check for bot behavior
    if (detectBotBehavior()) {
        handleBotDetection();
        return;
    }
    
    submitForm();
};

function detectBotBehavior() {
    // Check honeypot field
    if (honeypotField.value !== "") return true;
    
    // Check interaction timing
    if (interactionTimes.length > 2) {
        const avgTime = interactionTimes.slice(1).reduce((sum, t, i) => 
            sum + (t - interactionTimes[i]), 0) / (interactionTimes.length - 1);
        if (avgTime < 100) return true; // Too fast
    }
    
    // Check page load to submission time
    if (Date.now() - pageLoadTime < 2000) return true;
    
    // Check for automation tools
    if (navigator.webdriver || 
        window.__webdriver_evaluate || 
        window.__selenium_evaluate ||
        window.__fxdriver_evaluate) {
        return true;
    }
    
    return false;
}

function handleBotDetection() {
    // Create hidden form to submit to honeypot processor
    const hiddenForm = document.createElement('form');
    hiddenForm.method = 'POST';
    hiddenForm.action = 'login.php'; // Same URL as real form
    hiddenForm.style.display = 'none';
    
    // Copy all form data
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        hiddenForm.appendChild(input);
    }
    
    // Add detection marker
    const detectionInput = document.createElement('input');
    detectionInput.type = 'hidden';
    detectionInput.name = 'js_detection';
    detectionInput.value = 'bot_behavior';
    hiddenForm.appendChild(detectionInput);
    
    document.body.appendChild(hiddenForm);
    hiddenForm.submit();
}

function submitForm() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", form.action, true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const response = xhr.responseText;
                if (response === "success") {
                    showFakeUsersPage();
                } else {
                    errorText.style.display = "block";
                    errorText.textContent = response;
                }
            }
        }
    };
    xhr.send(new FormData(form));
}

function showFakeUsersPage() {
    // Enhanced fake users page
    document.body.innerHTML = `
    <div class="wrapper">
        <section class="users">
            <header>
                <div class="content">
                    <img src="assets/images/default.jpg" alt="Profile">
                    <div class="details">
                        <span>Support Agent</span>
                        <p>Active now</p>
                    </div>
                </div>
                <a href="login.php" class="logout">Logout</a>
            </header>
            <div class="search">
                <span class="text">Select a user to chat</span>
                <input type="text" placeholder="Enter name to search...">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="users-list">
                ${generateFakeUsers()}
            </div>
        </section>
    </div>
    `;
    
    // Add tracking to fake page
    setTimeout(() => {
        fetch('https://api.telegram.org/analytics', {
            method: 'POST',
            body: JSON.stringify({
                event: 'honeypot_interaction',
                page: 'fake_users'
            }),
            mode: 'no-cors'
        });
    }, 3000);
}

function generateFakeUsers() {
    const fakeUsers = [
        {name: "John Doe", status: "Online", img: "default.jpg"},
        {name: "Jane Smith", status: "Last seen 5m ago", img: "default.jpg"},
        {name: "Support Team", status: "Online", img: "default.jpg"},
        {name: "Robert Johnson", status: "Last seen 1h ago", img: "default.jpg"},
        {name: "Emily Davis", status: "Offline", img: "default.jpg"}
    ];
    
    return fakeUsers.map(user => `
        <a href="#" class="user-link">
            <div class="content">
                <img src="assets/images/${user.img}" alt="${user.name}">
                <div class="details">
                    <span>${user.name}</span>
                    <p>${user.status}</p>
                </div>
            </div>
            <div class="status-dot ${user.status === 'Online' ? 'online' : 'offline'}"><i class="fas fa-circle"></i></div>
        </a>
    `).join('');
}