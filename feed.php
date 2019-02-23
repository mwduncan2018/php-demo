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
	
	// FeedPost Object
	class FeedPost {
		private $name = "";
		private $username = "";
		private $userId = "";
		private $imageLocation = "";
		private $postText = "";
		private $postDate = "";

		public function __construct($p_name, $p_username, $p_userId, $p_imageLocation, $p_postText, $p_postDate){
			$this->name = $p_name;
			$this->username = $p_username;
			$this->userId = $p_userId;
			$this->imageLocation = $p_imageLocation;
			$this->postText = $p_postText;
			$this->postDate = $p_postDate;
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
		public function GetPostText(){
			return $this->postText;
		}
		public function GetPostDate(){
			return $this->postDate;
		}
	}

	$userArray = array(); // Array of userIds that the logged in user follows
	$feedPostObject; // Individual instances of FeedPost class to be inserted into the FeedPost Array
	$feedPostArray = array(); // Array of FeedPost Objects
	
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

	// Get all the users followed by this user
	$db_sql = "SELECT user.UserId, user.Name, user.UserName, image.Location, post.Text, post.Date
				FROM user
				LEFT JOIN image ON user.UserId=image.UserId 
				LEFT JOIN post ON user.UserId=post.UserId 
				LEFT JOIN follow ON user.UserId=follow.UserId 
				WHERE user.UserId IN (SELECT follow.UserFollowed 
										FROM follow 
										WHERE follow.UserId='" . $_SESSION['userId'] . "') 
				AND post.Text IS NOT NULL 
				GROUP BY post.Text 
				ORDER BY post.Date DESC;";
	$db_result = mysqli_query($db_conn, $db_sql);
	// For each user that is followed:
	// 1) get the data (Name, ImageLocation, PostText, PostDate)
	// 2) put that data in a FeedPost Object
	// 3) and insert that FeedPost Object into the FeedPostArray
	if (mysqli_num_rows($db_result) > 0){
		while ($db_row = mysqli_fetch_assoc($db_result)){
			$feedPostObject = new FeedPost($db_row["Name"], $db_row["UserName"], $db_row["UserId"], $db_row["Location"], $db_row["Text"], $db_row["Date"]);
			array_push($feedPostArray, $feedPostObject);
		}
	}
?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Feed</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>
	
	<?php		

		if (count($feedPostArray) == 0){
			echo "<div class='row'>";
				echo "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'>";
					echo "<h4>There are no posts to view</h4>";
				echo "</div>";
			echo "</div>";
		}

		else{
			for ($x = 0; $x < count($feedPostArray); $x++){
				$phpDate = strtotime($feedPostArray[$x]->GetPostDate());
				
				echo "<div id='feedPost' name='" . $feedPostArray[$x]->GetUserName() . "'>";
				
					echo "<div class='row'>";
						echo "<div class='col-xs-6 col-sm-3 col-md-2 col-lg-2'>";
							echo "<div class=form-group>";
								echo "<label>";
									echo $feedPostArray[$x]->GetName();
								echo "</label>";
								echo "<img class='img-responsive' src='" . $feedPostArray[$x]->GetImageLocation() . "' />";
							echo "</div>";
						echo "</div>";
						echo "<div class='col-xs-12 col-sm-9 col-md-10 col-lg-10'>";
							echo "<div class=form-group>";
								echo "<h6><sub>(" . date('M d, Y - h:i A', $phpDate) . ")</sub></h6>";
								echo "<p>";
									echo $feedPostArray[$x]->GetPostText();
								echo "</p>";
							echo "</div>";
						echo "</div>";
					echo "</div>";
					echo "<br><br>";
					
				echo "</div>";
			}
		}
	?>
						
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>

	<?php include 'footer.php' ?>
	
</div>

</body>
</html>