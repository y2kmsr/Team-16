<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="login.css">       <!--  style sheet connected -->
</head>

<body>
    <div class="container" id="SignUp" style="display: none;">      <!--  The sign up form is displayed as none so on activation the sign up form will be displayed -->
        <h1 class="form-title">Register</h1>
        <form method="post" action="register.php">      <!--  creating the sign up form and connecting it to the register.php page where the information will be stored and sent to the database -->
            <div class="input-group">
                <input type="text" name="fname" id="fname" placeholder="First Name" required>   <!--  creating the field first name on the form -->
                <label for="fname">First Name</label>

            </div>
            <div class="input-group">
                <input type="text" name="lname" id="lname" placeholder="Last name" required>    <!--  creating the field second name on the form -->
                <label for="lname">Last Name</label>
            </div>

            <div class="input-group">
                <input type="email" name="email" id="email" placeholder="Email" required>       <!--  creating the field email on the form -->
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <input type="number" name="phonenumber" id="phonenumber" placeholder="Phone number" required>       <!--  creating the phone number field on the form -->
                <label for="phonenumber">Phone Number</label>
            </div>
            <div class="input-group">
                <input type="text" name="address1" id="address1" placeholder="First Line of Address" required>      <!--  creating the addres field on the form -->
                <label for="address1">First Line of Address</label>
            </div>

            <div class="input-group">
                <input type="text" name="postalcode" id="postalcode" placeholder="Postal Code" required>            <!--  creating the postalcode field on the form -->
                <label for="postalcode">Postal Code</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>               <!--  creating the password field on the form -->
                <label for="password">Password</label>
            </div>
            <input type="submit" class="btn" value="Sign Up" name="signUp">     <!--  submit input type which collects the form contents -->                                     

        </form>

        <div class="links">
            <p>Already have an account?</p>
            <button id="signInButton">Sign in</button>      <!--  link which activates the sign in form and hides the sign up form -->
        </div>

    </div>

    <div class="container" id="SignIn">     <!--  creation of sign in form -->
        <form method="post" action="register.php">      
            <h1 class="form-title">Sign In</h1>
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder="Email" required>       <!--  creation of email field -->
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>       <!--  creation of password field -->
                <label for="password">Password</label>
            </div>

            <input type="submit" class="btn" value="Sign In" name="signIn">     <!--  submit input type which collects the form contents -->        
        </form>
        <div class="links">
            <p>Dont have an account?</p>
            <button id="signUpButton">Sign Up</button>       <!--  link which activates the sign up form and hides the sign in form -->
        </div>
    </div>

    <script src="script.js"></script>    <!-- connects the script file to the page -->
</body>

</html>