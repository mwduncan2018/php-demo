
<?php
	session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);

	header("Location: http://localhost/socialnetwork/login.php/");
	
	
	
	/*
		Format the "logged in as ...." and "not logged in " text at the top of the Navbar
		then continue with implementing any other page
	
	
	
	
	*/
	
?>