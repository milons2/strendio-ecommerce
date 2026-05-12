<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Create Account</h2>
            <form id="registrationForm" method="POST" action="register.php">
                <div class="mb-3">
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <label for="contactType" class="form-label">Register with:</label>
                    <select class="form-select" id="contactType" name="contactType">
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                    </select>
                </div>

                <div id="emailField" class="mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                    <span id="emailError" class="error"></span>
                </div>
                <div id="phoneField" style="display: none;" class="mb-3">
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone">
                    <span id="phoneError" class="error"></span>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="address" name="address" placeholder="Address"></textarea>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                    <span id="passwordError" class="error"></span>
                </div>

                <button type="submit" class="btn btn-success w-100">Register</button>
                <div class="text-center already-account">
                    <p>Already have an account? <a href="login_form.php" class="sign-in-link">Sign in</a></p>
                </div>
                <div id="message" class="mt-3"></div>
            </form>
        </div>
    </div>
</div>

<script src="register.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>