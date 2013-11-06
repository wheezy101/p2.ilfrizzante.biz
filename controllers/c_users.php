<?php
class users_controller extends base_controller {

    public function __construct() {
        
        parent::__construct();
        #echo "users_controller construct called<br><br>";
    
    } 

    public function index() {
    
        echo "This is the index page";
    
    }

    public function signup($error = NULL) {
    
        # Setup view
        $this->template->content = View::instance('v_users_signup');
        $this->template->title   = "Sign Up";

        # Pass data to the view
        $this->template->content->error = $error;

        # Render template
        echo $this->template;
    
    }

    public function p_signup() {                   
    
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);
        
        # Search the db for this email and password
        # Retrieve the token if it's available
        $q = "SELECT token 
            FROM users 
            WHERE email = '".$_POST['email']."'";
            
        $token = DB::instance(DB_NAME)->select_field($q);
        $email = $_POST['email'];
        $password = $_POST['password'];
        $first_name=$_POST['first_name'];
        $last_name=$_POST['last_name'];
        
        # Email address already in use or key fields are blank- fail
        if($token || (empty($email) || empty($password) || empty($first_name) || empty($last_name))){
            Router::redirect("/users/signup/error"); 
        }
        
        # Email address ok - pass
        else {
        # More data we want stored with the user
        $_POST['created']  = Time::now();
        $_POST['modified'] = Time::now();

        # Encrypt the password  
        $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);            

        # Create an encrypted token via their email address and a random string
        $_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string()); 

        # Insert this user into the database 
        $user_id = DB::instance(DB_NAME)->insert("users", $_POST);

        # Confirm they've signed up - 
        # Setup view
        $this->template->content = View::instance('v_users_confirm_signup');

        # Render template
        echo $this->template;
        
        }
    }

    public function reset($error = NULL) {
        
        # Set up the view
        $this->template->content = View::instance('v_users_reset');

        # Pass data to the view
        $this->template->content->error = $error;

        # Render the view
        echo $this->template;
        
    }

    public function p_reset() {

        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);

        # Search the db for this email
        # Retrieve the token if it's available
        $q = "SELECT user_id
            FROM users
            WHERE email = '".$_POST['email']."'"; 

        $user_id = DB::instance(DB_NAME)->select_field($q);

        # Email not the same - failed
        if(!$user_id) {
            Router::redirect('/users/reset/error'); 
        }  
        
        # Email passed
         else {
            
        # Encrypt the password  
        $new_password = sha1(PASSWORD_SALT.$_POST['new_password']);            

        # Update database with new password
        DB::instance(DB_NAME)->update("users", Array("password" => $new_password), "WHERE user_id = ".$user_id);
                
        # Confirm they've reset the password - 
        # Setup view
        $this->template->content = View::instance('v_users_confirm_reset');

        # Render template
        echo $this->template;

        }
    }

    public function login($error = NULL) {

        # Set up the view
        $this->template->content = View::instance('v_users_login');

        # Pass data to the view
        $this->template->content->error = $error;

        # Render the view
        echo $this->template;

    }
    
    public function p_login() {
        
        # Sanitize the user entered data to prevent any funny-business (re: SQL Injection Attacks)
        $_POST = DB::instance(DB_NAME)->sanitize($_POST);
        
        # Hash submitted password so we can compare it against one in the db
        $_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
        
        # Search the db for this email and password
        # Retrieve the token if it's available
        $q = "SELECT token 
            FROM users 
            WHERE email = '".$_POST['email']."' 
            AND password = '".$_POST['password']."'";
        $token = DB::instance(DB_NAME)->select_field($q);
        
        # Login failed
        if(!$token) {
        
        # Note the addition of the parameter "error"
            Router::redirect("/users/login/error"); 
        }
        
        # Login passed
        else {
            setcookie("token", $token, strtotime('+2 weeks'), '/');
            Router::redirect("/");
    }
}
    
    public function logout() {

        # Generate and save a new token for next login
        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

        # Create the data array we'll use with the update method
        # In this case, we're only updating one field, so our array only has one entry
        $data = Array("token" => $new_token);

        # Do the update
        DB::instance(DB_NAME)->update("users", $data, "WHERE token = '".$this->user->token."'");
        
        # Delete their token cookie by setting it to a date in the past - effectively logging them out
        setcookie("token", "", strtotime('-1 year'), '/');

        # Send them back to the main index.
        Router::redirect('/');

    }
    
    public function profile() {

        # If user is blank, they're not logged in; redirect them to the login page
        if(!$this->user) {
            Router::redirect('/users/login');
        }

        # If they weren't redirected away, continue:

        # Setup view
        $this->template->content = View::instance('v_users_profile');
        $this->template->title   = "Profile of".$this->user->first_name;

        # Render template
        echo $this->template;
}

} # end of the class
