document.getElementById('contactType').addEventListener('change', function () {
    document.getElementById('emailField').style.display = this.value === 'email' ? 'block' : 'none';
    document.getElementById('phoneField').style.display = this.value === 'phone' ? 'block' : 'none';

    document.getElementById('email').required = this.value === 'email';
    document.getElementById('phone').required = this.value === 'phone';
});

const form = document.getElementById('registrationForm');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const passwordError = document.getElementById('passwordError');
const email = document.getElementById('email');
const phone = document.getElementById('phone');
const messageDiv = document.getElementById('message');

// Password check
confirmPassword.addEventListener('keyup', () => {
    passwordError.textContent =
        password.value !== confirmPassword.value
            ? "Passwords do not match"
            : "";
});

// Availability check
email.addEventListener('blur', checkAvailability);
phone.addEventListener('blur', checkAvailability);

function checkAvailability(event) {
    const field = event.target;
    const value = field.value;
    const type = field.id;
    const errorSpan = document.getElementById(type + 'Error');

    if (value === "") {
        errorSpan.textContent = "";
        return;
    }

    fetch('check.php?type=' + type + '&value=' + value)
        .then(res => res.json())
        .then(data => {
            errorSpan.textContent = data.message;
        });
}

// ✅ Submit
form.addEventListener('submit', function (event) {
    event.preventDefault();

    if (password.value !== confirmPassword.value) {
        alert("Passwords do not match");
        return;
    }

    const formData = new FormData(form);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            messageDiv.textContent = data.message;

            if (data.status === 'success') {
                form.reset();

                // ✅ Redirect to LOGIN PAGE (NOT API)
                setTimeout(() => {
                    window.location.href = "login_form.php"; // 👈 IMPORTANT
                }, 1500);
            }
        })
        .catch(() => {
            messageDiv.textContent = "Something went wrong";
        });
});