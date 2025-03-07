const signInButton = document.getElementById('signInButton');
const signUpButton = document.getElementById('signUpButton');
const signInForm = document.getElementById('SignIn');
const signUpForm = document.getElementById('SignUp'); 

signInButton.addEventListener('click', function() {
    signInForm.style.display = "block";
    signUpForm.style.display = "none";

});

signUpButton.addEventListener('click', function(){
    signUpForm.style.display = "block";
    signInForm.style.display = "none";
}); 

/*handles the links "dont have an account" & "already have an account" only 1 form is displayed at a time*/
