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
	// Redirect to Login page if no user is logged in
	if (!isset($_SESSION["username"])){
		header("Location: http://localhost/socialnetwork/login.php/");
		exit;		
	}
	
	// Define Page Variable(s)
	$webmaster = "Mike Duncan";
	$name = "";
	$username = "";
	$location = "";

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

	// Get info from DB
	$db_sql = "SELECT User.UserId, User.Name, User.UserName, User.AboutMe, Image.Location 
				FROM User 
				INNER JOIN Image 
				ON User.UserId=Image.UserId
				WHERE User.UserId = '" . $_SESSION["userId"] . "';";
	$db_result = mysqli_query($db_conn, $db_sql);
	$db_rowCount = mysqli_num_rows($db_result);
	if ($db_rowCount > 0){
		if (mysqli_num_rows($db_result) > 0){
			while ($db_row = mysqli_fetch_assoc($db_result)){
				$name = $db_row["Name"];
				$username = $db_row["UserName"];
				$aboutMe = $db_row["AboutMe"];
				$location = $db_row["Location"];
			}
		}
	}
	else{
		echo "Error executing query...<br><br>" . $db_sql;
		exit;
	}
	
	// Close DB Connection
	mysqli_close($db_conn);				
?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>View My Profile</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
			
			<div class="form-group">
				<img class="img-responsive" src="<?php echo $location ?>" />
			</div>

		</div>
		<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
		
			<div class="form-group">
				<label>Name:</label>
				<p><?php echo $name ?></p>
			</div>
			<div class="form-group">
				<label>Username:</label>
				<p><?php echo $username ?></p>
			</div>
			<div class="form-group">
				<label>About Me:</label>
				<p><?php echo $aboutMe ?></p>
			</div>
			
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