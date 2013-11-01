<h1>This is the profile of <?=$user->first_name?></h1>

public function profile($user_name = NULL) {

    # Create a new View instance
    # Do *not* include .php with the view name
    $view = View::instance('v_users_profile');

    # Pass information to the view instance
    $view->user_name = $user_name;

    # Render View
    echo $view;

}
