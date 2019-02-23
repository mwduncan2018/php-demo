<?php
session_start();
/*
	My 1st PHP Website
	"Minimalist Social Network"	
*/
?>

<!DOCTYPE html>
<html>
<head>
	<?php include 'head.php' ?>
</head>
<body class='custom-fade'>

<?php
	// Define Session Variable(s)
	//$_SESSION['userId'] = "";
	// Define Page Variable(s)
	$webmaster = "Mike Duncan";
	$username = "";
	$password = "";
	$submitError = "";
	
	// If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		// Remove white space and encode special chars
		$username = test_input($_POST["txtUsername"]);			
		$password = test_input($_POST["txtPassword"]);

		// Define DB Variables
		$db_server = "localhost";
		$db_username = "root";
		$db_password = "";
		$db_database = "socialnetworkdb1";
		
		// Create DB Connection
		$db_conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
		if (!$db_conn){
			die("Connection Failed: " . mysqli_connect_error());
		}

		// Check if the Username/Password combo is valid
		$db_sql = "SELECT UserId, UserName, Password FROM User WHERE UserName = '" . $_POST["txtUsername"] . "'";
		$db_result = mysqli_query($db_conn, $db_sql);
		if (mysqli_num_rows($db_result) > 0){
			while ($db_row = mysqli_fetch_assoc($db_result)){
				if (($db_row["UserName"] == $_POST["txtUsername"]) && ($db_row["Password"] == $_POST["txtPassword"])){
					$_SESSION['userId'] = $db_row["UserId"];
					$_SESSION['username'] = $db_row["UserName"];
					header("Location: http://localhost/socialnetwork/viewmyprofile.php/");
					exit;
				}
			}
		}
		$submitError = "* Username/Password combination is invalid";
	}
	
	// This function is used to trim white space
	// and encode html special characters.
	function test_input($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Login</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >

				<div class="form-group">
					<label for="txtUsername">Username:</label>
					<input class="form-control" value="<?php echo $username ?>" type="text" name="txtUsername" id="txtUsername" >
				</div>

				<div class="form-group">
					<label for="txtPassword">Password:</label>
					<input class="form-control" value="<?php echo $password ?>" type="password" name="txtPassword" id="txtPassword" >
				</div>
				
				<div>
					<button id="submit" name="submit" type="submit" class="btn btn-primary">Login</button>
					<span class="text-danger"><?php echo $submitError ?></span>						
				</div>

			</form>

		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>

	<?php include 'footer.php' ?>
	
</div>

</body>
</html>