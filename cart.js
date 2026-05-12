document.addEventListener('DOMContentLoaded', () => {
    const removeButtons = document.querySelectorAll('.remove-btn');

    removeButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                e.preventDefault(); // Cancel removal
            }
        });
    });

    const updateForm = document.querySelector('form');
    if (updateForm) {
        updateForm.addEventListener('submit', () => {
            alert('Cart updated successfully!');
        });
    }
});

