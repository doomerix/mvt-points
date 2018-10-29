<?php

/* Include config file */
require_once './db/connection.php';
require_once 'security.php';

//$_SESSION['user'] = 'random string';
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //get user_id & password from post
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE user_id = ?;";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $user_id);

    //has the $sql query been propery executed or not?
    if ($stmt->execute() == false)
    {
        //Query failed
        print 'Je kon niet worden ingelogd.';
        exit;
    }
    
    $result = $stmt->get_result();

    //given user_id can't be found, 0 records are returned/found
    if ($result->num_rows === 0)
    {
        print 'Verkeerde gebruikersnaam en wachtwoord combinatie.';
        exit;
    }

    $user = $result->fetch_assoc();

    //when the query has succeded and a record has been found we will compare the password with the passwordhash from the database
    if (password_verify($password, $user['passwordhash']))
    {
        print 'Aanmelden gelukt.<br />';

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['roll_id'] = $user['roll_id'];
        
        $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        
        /* Redirect to Index page */
        header('Location: index.html');
    }
    else
    {
        print 'Verkeerde gebruikersnaam en wachtwoord combinatie.';
    }
    $result->free();
    $connection->close();
}
else
{
    print 'Aanmelden mislukt!';
}

//if a logged-in user should nevertheless return to this login page, he/she will be sent back to the index page
if (isset($_SESSION['user_id']))
{
    header('Location: index.html');
}
?>
