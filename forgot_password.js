document.getElementById("forgotPasswordForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const email = document.getElementById("forgotContact").value;
    const forgotMessage = document.getElementById("forgotMessage");

    // Clear previous messages
    forgotMessage.textContent = "";
    forgotMessage.className = "";

    try {
        const response = await fetch("forgot_password.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ email: email }),
        });

        const result = await response.json();

        if (response.ok) {
            // Success message
            forgotMessage.textContent = result.message;
            forgotMessage.className = "alert alert-success";
        } else {
            // Error message
            forgotMessage.textContent = result.message;
            forgotMessage.className = "alert alert-danger";
        }
    } catch (error) {
        forgotMessage.textContent = "An error occurred. Please try again.";
        forgotMessage.className = "alert alert-danger";
    }
});
