document.addEventListener('DOMContentLoaded', function () {
    const profileCard = document.querySelector('.profile-card');
    const greetingElement = document.querySelector('.profile-card h2');

    // Fetch session data from PHP using AJAX
    fetch('get_user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const fullName = data.full_name;
                const greeting = getGreetingMessage();
                greetingElement.textContent = `${greeting}, ${fullName}!`;
            } else {
                console.error('Error:', data.message);
                window.location.href = 'login.html'; // Redirect to login if session not found
            }
        })
        .catch(error => {
            console.error('Error fetching user info:', error);
            window.location.href = 'login.html';
        });
});

/**
 * Returns the appropriate greeting message based on the current time.
 */
function getGreetingMessage() {
    const now = new Date();
    const hours = now.getHours();

    if (hours >= 5 && hours < 12) {
        return "Good Morning";
    } else if (hours >= 12 && hours < 17) {
        return "Good Afternoon";
    } else if (hours >= 17 && hours < 21) {
        return "Good Evening";
    } else {
        return "Good Night";
    }
}
