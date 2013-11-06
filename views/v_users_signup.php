<form method='POST' action='/users/p_signup'>

    First Name<br>
    <input type='text' name='first_name'>
    <br><br>

    Last Name<br>
    <input type='text' name='last_name'>
    <br><br>

    Email<br>
    <input type='text' name='email'>
    <br><br>

    Password<br>
    <input type='password' name='password'>
    <br><br>
    
    Location<br>
    <input type='text' name='location'>
    <br><br>
    
    Gender<br>
    <input type='text' name='gender'>
    <br><br>

    Age<br>
    <input type='int' name='age'>
    <br><br>
     
    <label for='bio'>Bio</label><br>
    <textarea name='bio' name='bio'></textarea>
    <br><br>
            <?php if(isset($error)): ?>
        <div class='error'>
            Signup failed. Please try again with a new email address.
            Also make sure first name, last name, email, and password fields are not blank.
        </div>
        <br>
    <?php endif; ?>

    <input type='submit' value='Sign up'>


</form>

    



