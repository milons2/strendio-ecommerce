const profileForm = document.getElementById('profileForm');

profileForm.addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(profileForm);

    fetch('profile_update.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Profile updated successfully!');
                window.location.reload();
            } else {
                alert('Error updating profile: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
});
