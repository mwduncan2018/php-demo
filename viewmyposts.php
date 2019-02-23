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
	$postId = "";
	$text = "";
	$postDate = "";
	$submitSuccessError = "";
	
	// Post Object
	class Post {
		private $postId = "";
		private $text = "";
		private $postDate ="";
		
		public function __construct($p_postId, $p_text, $p_date){
			$this->postId = $p_postId;
			$this->text = $p_text;
			$this->postDate = $p_date;
		}

		public function GetPostId(){
			return $this->postId;
		}
		public function GetPostText(){
			return $this->text;
		}
		public function GetPostDate(){
			return $this->postDate;
		}		
	}
	
	$postObject; // Individual instances of Post class to be inserted into the Post Array
	$postArray = array(); // Array of Post Objects		
	
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

	// SELECT all Posts by this User
	$db_sql = "SELECT PostId, Text, Date
				FROM Post
				WHERE UserId = '" . $_SESSION["userId"] . "'
				ORDER BY Date DESC;";
	$db_result = mysqli_query($db_conn, $db_sql);
	if (mysqli_num_rows($db_result) > 0){
		while ($db_row = mysqli_fetch_assoc($db_result)){
			$postObject = new Post($db_row["PostId"], $db_row["Text"], $db_row["Date"]);
			array_push($postArray, $postObject);
		}
	}

?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>View My Posts</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>

	<div class="row">		
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<?php
			
				if (count($postArray) == 0){
					echo "<h4>There are no posts to view</h4>";						
				}else{
					for ($x = 0; $x < count($postArray); $x++){
						$phpDate = strtotime($postArray[$x]->GetPostDate());
						echo "<div class='form-group'>";
							echo "<label>";
								echo date('M d, Y - h:i A', $phpDate);
							echo "</label>";
							echo "<p>";
								echo $postArray[$x]->GetPostText();
							echo "</p>";
						echo "</div>";
						echo "<br><br>";
					}
				}
				
			?>
			
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