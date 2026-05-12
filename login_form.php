<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Login</h2>
            <form id="loginForm" method="POST" action="login.php">
                <div class="mb-3">
                    <label for="loginContact" class="form-label">Email or Phone:</label>
                    <input type="text" class="form-control" id="loginContact" name="loginContact" placeholder="Enter your registered email or phone" required>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password:</label>
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="text-center mt-2">
                    <a href="forgot_password_form.php" class="forgot-password-link">Forgot Password?</a>
                </div>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="register_form.php" class="sign-up-link">Sign Up</a></p>
                </div>
                <div id="loginMessage" class="mt-3"></div>
            </form>
        </div>
    </div>
</div>

<script src="login.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>