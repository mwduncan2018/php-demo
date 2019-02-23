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
	$submitSuccessError = "";
	
	// Step 2: If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

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
			
		// Delete from the server
		// 1) Get the File Location from the Image table
		// 2) Delete the image at that file location
		$db_sql = "SELECT Image.Location
					FROM User
					INNER JOIN Image
					ON User.UserId=Image.UserId
					WHERE User.UserId = '" . $_SESSION["userId"] . "';";
		$db_result = mysqli_query($db_conn, $db_sql);
		$db_rowCount = mysqli_num_rows($db_result);
		if ($db_rowCount > 0){
			if (mysqli_num_rows($db_result) > 0){
				while ($db_row = mysqli_fetch_assoc($db_result)){
					$filePath = substr($db_row["Location"], 15);
					unlink($filePath);
				}
			}
		}
		else{
			echo "Error executing query...<br><br>" . $db_sql;
			exit;
		}
	
		// Delete from the Image table
		// 1) Where UserId is "Session UserId"
		$db_sql = "DELETE FROM Image
					WHERE UserId = '" . $_SESSION["userId"] . "';";
		if (!mysqli_query($db_conn, $db_sql)){
			echo "Could not delete record: " . mysqli_error($db_conn) . "<br><br>";
		}		
		
		// Delete from the Follow table
		// 1) Where UserId is "Session UserId"
		// 2) Where UserFollowed is "Logged In UserId"
		$db_sql = "DELETE FROM Follow
					WHERE UserId = '" . $_SESSION["userId"] . "';";
		if (!mysqli_query($db_conn, $db_sql)){
			echo "Could not delete record: " . mysqli_error($db_conn) . "<br><br>";
		}		
		$db_sql = "DELETE FROM Follow
					WHERE UserFollowed = '" . $_SESSION["userId"] . "';";
		if (!mysqli_query($db_conn, $db_sql)){
			echo "Could not delete record: " . mysqli_error($db_conn) . "<br><br>";
		}		
		
		// Delete from the Post table
		// 1) Where UserId is "Session UserId"
		$db_sql = "DELETE FROM Post
					WHERE UserId = '" . $_SESSION["userId"] . "';";
		if (!mysqli_query($db_conn, $db_sql)){
			echo "Could not delete record: " . mysqli_error($db_conn) . "<br><br>";
		}		
				
		// Delete from the User table
		// 1) Where UserId is "Session UserId"
		$db_sql = "DELETE FROM User
					WHERE UserId = '" . $_SESSION["userId"] . "';";
		if (!mysqli_query($db_conn, $db_sql)){
			echo "Could not delete record: " . mysqli_error($db_conn) . "<br><br>";
		}
		
		// Close DB Connection
		mysqli_close($db_conn);						
	
		// End the User Session by going to the Logout page, which will redirect to the Login Page after ending the session
		header("Location: http://localhost/socialnetwork/logout.php/");
		exit;
	}

?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Delete My Profile</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>
	
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >

		<div class="row">		
			<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
				<div class="form-group">
					<label for="txtName" style="color:red">Warning:</label>
					<p style="color:red" >Clicking the 'Delete Profile' button will delete this user and all associated data from the database.</p>
				</div>
			</div>
		</div>
		<div class="row">		
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<button id="submit" name="submit" type="submit" class="btn btn-primary">Delete Profile</button><br>
					<span class="text-success"><?php echo $submitSuccessError ?></span>
				</div>
			</div>
		</div>
		
	</form>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>

	<?php include 'footer.php' ?>
	
</div>

</body>
</html>