// Toggle forms with animation
const loginBtn = document.getElementById('loginBtn');
const signupBtn = document.getElementById('signupBtn');
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');

// Function to show login form
function showLogin() {
    loginForm.classList.add('active');
    signupForm.classList.remove('active');
    loginBtn.classList.add('active');
    signupBtn.classList.remove('active');
}

// Function to show signup form
function showSignup() {
    loginForm.classList.remove('active');
    signupForm.classList.add('active');
    signupBtn.classList.add('active');
    loginBtn.classList.remove('active');
}

// Event listeners for buttons
loginBtn.addEventListener('click', showLogin);
signupBtn.addEventListener('click', showSignup);

// عرض Login عند تحميل الصفحة (Show login on page load)
document.addEventListener('DOMContentLoaded', () => {
    showLogin(); // Ensure login is active by default
});
