<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <!-- Sign Up Form -->
    <div class="container" id="SignUp" style="display: none;">
        <h1 class="form-title">Register</h1>
        <form method="post" action="register.php" onsubmit="return validateSignUpForm()">
            <div class="input-group">
                <input type="text" name="fname" id="fname" placeholder="First Name" required>
                <label for="fname">First Name</label>
                <span class="error" id="fnameError"></span>
            </div>
            <div class="input-group">
                <input type="text" name="lname" id="lname" placeholder="Last Name" required>
                <label for="lname">Last Name</label>
                <span class="error" id="lnameError"></span>
            </div>
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder="Email" required>
                <label for="email">Email</label>
                <span class="error" id="emailError"></span>
            </div>
            <div class="input-group">
                <input type="text" name="phonenumber" id="phonenumber" placeholder="Phone Number" required>
                <label for="phonenumber">Phone Number</label>
                <span class="error" id="phonenumberError"></span>
            </div>
            <div class="input-group">
                <input type="text" name="address1" id="address1" placeholder="First Line of Address" required>
                <label for="address1">First Line of Address</label>
                <span class="error" id="address1Error"></span>
            </div>
            <div class="input-group">
                <input type="text" name="postalcode" id="postalcode" placeholder="Postal Code" required>
                <label for="postalcode">Postal Code</label>
                <span class="error" id="postalcodeError"></span>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="password">Password</label>
                <span class="error" id="passwordError"></span>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">
        </form>

        <div class="links">
            <p>Already have an account?</p>
            <button id="signInButton">Sign In</button>
        </div>
    </div>

    <!-- Sign In Form -->
    <div class="container" id="SignIn">
        <form method="post" action="register.php" onsubmit="return validateSignInForm()">
            <h1 class="form-title">Sign In</h1>
            <div class="input-group">
                <input type="email" name="email" id="signInEmail" placeholder="Email" required>
                <label for="signInEmail">Email</label>
                <span class="error" id="signInEmailError"></span>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="signInPassword" placeholder="Password" required>
                <label for="signInPassword">Password</label>
                <span class="error" id="signInPasswordError"></span>
            </div>
            <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
        <div class="links">
            <p>Don't have an account?</p>
            <button id="signUpButton">Sign Up</button>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Validation for Sign Up Form
        function validateSignUpForm() {
            let isValid = true;

            // Reset error messages
            document.querySelectorAll('.error').forEach(error => error.textContent = '');

            // First Name: letters only, min 2 characters
            const fname = document.getElementById('fname').value.trim();
            const fnamePattern = /^[A-Za-z]{2,}$/;
            if (!fnamePattern.test(fname)) {
                document.getElementById('fnameError').textContent = 'First name must be at least 2 characters long and contain only letters.';
                isValid = false;
            }

            // Last Name: letters only, min 2 characters
            const lname = document.getElementById('lname').value.trim();
            const lnamePattern = /^[A-Za-z]{2,}$/;
            if (!lnamePattern.test(lname)) {
                document.getElementById('lnameError').textContent = 'Last name must be at least 2 characters long and contain only letters.';
                isValid = false;
            }

            // Email: valid email format
            const email = document.getElementById('email').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('emailError').textContent = 'Please enter a valid email address.';
                isValid = false;
            }

            // Phone Number: valid format (e.g., +44 123 456 7890 or 01234567890)
            const phoneNumber = document.getElementById('phonenumber').value.trim();
            const phonePattern = /^\+?[0-9\s-]{10,15}$/;
            if (!phonePattern.test(phoneNumber)) {
                document.getElementById('phonenumberError').textContent = 'Please enter a valid phone number (e.g., +44 123 456 7890).';
                isValid = false;
            }

            // First Line of Address: min 5 characters
            const address1 = document.getElementById('address1').value.trim();
            if (address1.length < 5) {
                document.getElementById('address1Error').textContent = 'Address must be at least 5 characters long.';
                isValid = false;
            }

            // Postal Code: UK postal code format (e.g., SW1A 1AA)
            const postalCode = document.getElementById('postalcode').value.trim();
            const postalCodePattern = /^[A-Z]{1,2}[0-9R][0-9A-Z]? [0-9][A-Z]{2}$/i;
            if (!postalCodePattern.test(postalCode)) {
                document.getElementById('postalcodeError').textContent = 'Please enter a valid UK postal code (e.g., SW1A 1AA).';
                isValid = false;
            }

            // Password: min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
            const password = document.getElementById('password').value;
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordPattern.test(password)) {
                document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.';
                isValid = false;
            }

            return isValid;
        }

        // Validation for Sign In Form
        function validateSignInForm() {
            let isValid = true;

            // Reset error messages
            document.querySelectorAll('.error').forEach(error => error.textContent = '');

            // Email: valid email format
            const email = document.getElementById('signInEmail').value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById('signInEmailError').textContent = 'Please enter a valid email address.';
                isValid = false;
            }

            // Password: min 8 chars (to match Sign Up requirement)
            const password = document.getElementById('signInPassword').value;
            if (password.length < 8) {
                document.getElementById('signInPasswordError').textContent = 'Password must be at least 8 characters long.';
                isValid = false;
            }

            return isValid;
        }
    </script>
</body>
</html>
