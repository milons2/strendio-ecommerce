document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const messageDiv = document.getElementById("loginMessage");

    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();

            messageDiv.textContent = "";
            messageDiv.className = "";
            messageDiv.style.display = "none";

            const formData = new FormData(form);

            try {
                const response = await fetch("login.php", {
                    method: "POST",
                    body: formData,
                });

                const data = await response.json();

                if (data.status === "success") {
                    console.log("Login successful. Redirecting to dashboard.php via JavaScript.");
                    messageDiv.textContent = "Login successful! Redirecting...";
                    messageDiv.className = "alert alert-success";
                    messageDiv.style.display = "block";

                    setTimeout(() => {
                    window.location.href = "dashboard.php";
                    }, 1500);
                } else {
                    console.warn("Login error:", data.message);
                    messageDiv.textContent = data.message;
                    messageDiv.className = "alert alert-danger";
                    messageDiv.style.display = "block";
                }
            } catch (error) {
                console.error("Fetch error:", error);
                messageDiv.textContent = "An error occurred. Please try again later.";
                messageDiv.className = "alert alert-warning";
                messageDiv.style.display = "block";
            }
        });
    }
});