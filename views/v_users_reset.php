<form method='POST' action='/users/p_reset'>

    Email<br>
    <input type='text' name='email'>
    <br><br>

    New Password<br>
    <input type='text' name='new_password'>
    <br><br>

        <?php if(isset($error)): ?>
        <div class='error'>
            Reset password failed. Please double check your email and try a new password.
        </div>
        <br>
    <?php endif; ?>

    
    <input type='submit' value='Reset password'>

</form>



