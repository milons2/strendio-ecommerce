<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Reset Your Password</h2>
            <form id="forgotPasswordForm" method="POST" action="forgot_password.php">
                <div class="mb-3">
                    <label for="forgotContact" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="forgotContact" name="forgotContact" placeholder="Enter your registered email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                <div id="forgotMessage" class="mt-3"></div>
            </form>
        </div>
    </div>
</div>

<script src="forgot_password.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>