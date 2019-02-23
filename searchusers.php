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
	
	// If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		// This code figures out which button was clicked
		// 1) UserId was jammed into the HTML 'name' attribute
		// 2) The 'key' (below) is the 'name' of the button that was clicked
		// 3) That 'name' (aka UserId) can then be used to DELETE FROM the Follow table
		foreach ($_POST as $key => $value){

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

			// First, Figure out if we need to DELETE (aka Unfollow) or INSERT (aka Follow)
			$db_sql = "SELECT *
						FROM follow
						WHERE UserId='" . $_SESSION['userId'] . "' AND UserFollowed='" . $key . "';";
			$db_result = mysqli_query($db_conn, $db_sql);
			// If a record is returned from the query, that means we need to DELETE (aka Unfollow)
			if (mysqli_num_rows($db_result) > 0){
				// REMOVE the entry from the Follow table
				$db_sql = "DELETE FROM follow
							WHERE UserFollowed=" . $key . " AND UserId=" . $_SESSION['userId'] . ";";
				if (!mysqli_query($db_conn, $db_sql)){
					$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn) . "<br>";
				}
			}
			// If a record is not returned from the query, that means we need to INSERT (aka Follow)
			else{
				// INSERT the entry into the Follow table
				$db_sql = "INSERT INTO follow (UserFollowed, UserId)
							VALUES ('" . $key . "', '" . $_SESSION['userId'] . "');";
				if (!mysqli_query($db_conn, $db_sql)){
					$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn) . "<br>";
				}
			}

			// Close DB Connection
			mysqli_close($db_conn);
		
		}
	}
	
	
	// User Object
	class User {
		private $name = "";
		private $username = "";
		private $userId = "";
		private $aboutMe = "";
		private $imageLocation = "";
		private $followed = FALSE;

		public function __construct($p_name, $p_username, $p_userId, $p_aboutMe, $p_imageLocation){
			$this->name = $p_name;
			$this->username = $p_username;
			$this->userId = $p_userId;
			$this->aboutMe = $p_aboutMe;
			$this->imageLocation = $p_imageLocation;
		}

		public function GetName(){
			return $this->name;
		}
		public function GetUserName(){
			return $this->username;
		}
		public function GetUserId(){
			return $this->userId;
		}
		public function GetImageLocation(){
			return $this->imageLocation;
		}
		public function GetAboutMe(){
			return $this->aboutMe;
		}
		public function SetFollowed($p_followed){
			$this->followed = $p_followed;
		}
		public function DisplayButton(){
			$returnStr = "";
			if ($this->followed == TRUE){
				$returnStr = "<button id='" . $this->GetUserId() . "' name='" . $this->GetUserId() . "' type='submit' class='btn btn-warning'>Unfollow</button><br>";
				return $returnStr;
			}else{
				$returnStr = "<button id='" . $this->GetUserId() . "' name='" . $this->GetUserId() . "' type='submit' class='btn btn-success'>Follow</button><br>";
				return $returnStr;
			}
		}
	}

	$userObject; // Individual instances of User class to be inserted into the User Array
	$userObjectArray = array(); // Array of User Objects
	
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

	// Get data for all users excluding the current logged in user
	$db_sql = "SELECT user.UserId, user.Name, image.Location, user.AboutMe, user.UserName
				FROM user
				INNER JOIN image
				ON user.UserId=image.UserId
				WHERE user.UserId!='" . $_SESSION['userId'] . "'
				ORDER BY user.Name;";
	$db_result = mysqli_query($db_conn, $db_sql);
	// If there are users returned from the query
	if (mysqli_num_rows($db_result) > 0){
		// For each row returned from the query, build that data into a User Object and put that User Object into the Array of Users
		while ($db_row = mysqli_fetch_assoc($db_result)){
			$userObject = new User($db_row["Name"], $db_row["UserName"] ,$db_row["UserId"], $db_row["AboutMe"], $db_row["Location"]);		
			array_push($userObjectArray, $userObject);
		}		
	}

	// Get all Users followed by this User
	$db_sql = "SELECT follow.UserFollowed
				FROM follow
				WHERE follow.UserId=" . $_SESSION['userId'] . ";";
	$db_result = mysqli_query($db_conn, $db_sql);
	// If there are Users followed by this User...
	if (mysqli_num_rows($db_result) > 0){
		// Go through the Array of User Objects and set the 'followed' field to TRUE for all that are followed by this User
		while ($db_row = mysqli_fetch_assoc($db_result)){
			
			for ($x = 0; $x < count($userObjectArray); $x++){
				if ($userObjectArray[$x]->GetUserId() == $db_row["UserFollowed"]){
					$userObjectArray[$x]->SetFollowed(TRUE);
				}
			}
			
		}		
	}

	// Close DB Connection
	mysqli_close($db_conn);
?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Search Users</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>

	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >	
	
		<?php
		
			if (count($userObjectArray) == 0){
				echo "<div class='row'>";
					echo "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
						echo "<h4>You are the only existing user. There is no other user to follow.</h4>";
					echo "</div>";
				echo "</div>";
			}
			
			else{
				for ($x = 0; $x < count($userObjectArray); $x++){

					echo "<div id='" . $userObjectArray[$x]->GetUserName() . "'>";
				
						echo "<div class='row'>";
							echo "<div class='col-xs-6 col-sm-5 col-md-5 col-lg-5'>";
								
								echo "<div class='form-group'>";
									echo "<img class='img-responsive' src='" . $userObjectArray[$x]->GetImageLocation() . "' />";
								echo "</div>";
								
							echo "</div>";
							echo "<div class='col-xs-12 col-sm-7 col-md-7 col-lg-7'>";

								echo "<div class='form-group'>";
									echo "<label>Name:</label>";
									echo "<p id='name'>" . $userObjectArray[$x]->GetName() . "</p>";
								echo "</div>";
/*								echo "<div class='form-group'>";
									echo "<label>Username:</label>";
									echo "<p id='username'>" . $userObjectArray[$x]->GetUserName() . "</p>";
								echo "</div>";
*/								echo "<div class='form-group'>";
									echo "<label>About Me:</label>";
									echo "<p id='aboutme'>" . $userObjectArray[$x]->GetAboutMe() . "</p>";
								echo "</div>";

								echo "<div class='form-group'>";
									echo $userObjectArray[$x]->DisplayButton();
								echo "</div>";
									
							echo "</div>";
						echo "</div>";
						echo "<br><br>";

					echo "</div>";
				}				
			}
		
		?>
	
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