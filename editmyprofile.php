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
	$nameError = "";
	$location;
	$imageError = "";
	$aboutMe = "";
	$aboutMeError = "";
	$submitSuccessError = "";


	
	// Step 1: Populate fields with what is in the database right now

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

	
	
	// Step 2: If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		// Remove white space and encode special chars
		$name = test_input($_POST["txtName"]);
		$aboutMe = test_input($_POST["areaAboutMe"]);

		// Validations
		$aboutMeError = AboutMeValidation();
		$imageError = ImageValidation();
		$nameError = NameValidation();

		// If no validation errors...
		if ($imageError == "" && $aboutMeError == "" && $nameError == ""){

			//=========================
			// Update Image
			//=========================
		
			// If the user selected an image
			if ($_FILES["fileImage"]["tmp_name"] != ""){	
			
				// Delete the existing file on the server
				$delete_image_path = "C:/xampp/htdocs" . $location;
				if (!unlink($delete_image_path)){
					$submitSuccessError = "Error deleting file on the server<br>";
				}
				
				// Upload the image file to the server
				// If there is an error while uploading, display a message and DO NOT save the record to the "socialnetworkdb1" database
				$target_dir = "C:/xampp/htdocs/socialnetwork/Images/";
				$target_name = basename($_FILES["fileImage"]["name"]);
				$target_file = $target_dir . $target_name;
				if (!(move_uploaded_file($_FILES["fileImage"]["tmp_name"], $target_file))) {
					$submitSuccessError = "Sorry, there was an error uploading your file<br>";
				}
				// if the image was uploaded on the server successfully
				else{

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

					// UPDATE the image for this user
					$location = '/socialnetwork/Images/' . $target_name;
					$db_sql = "UPDATE Image
								SET Location='" . $location . "'
								WHERE UserId=" . $_SESSION["userId"];
					if (!mysqli_query($db_conn, $db_sql)){
						$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn) . "<br>";
					}
					
					// Close DB Connection
					mysqli_close($db_conn);
				}
			}

			//=========================
			// Update Name
			//=========================

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

			// Update User info
			$db_sql = "UPDATE User SET Name='" . $name . "' WHERE UserId=" . $_SESSION['userId'];
			if (!mysqli_query($db_conn, $db_sql)){
				$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
			}
			
			// Close DB Connection
			mysqli_close($db_conn);

			//=========================
			// Update AboutMe
			//=========================
			
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
			
			// Update User info
			$db_sql = "UPDATE User SET AboutMe='" . $aboutMe . "' WHERE UserId=" . $_SESSION['userId'];
			if (!mysqli_query($db_conn, $db_sql)){
				$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
			}

			// Close DB Connection
			mysqli_close($db_conn);			
		}

		// If "submitSuccessError" and all the validation strings are blank strings, that means no errors occurred, so the Profile was updated successfully
		if ($submitSuccessError == "" && $imageError == "" && $aboutMeError == "" && $nameError == ""){
			$submitSuccessError = "Profile Updated!";				
		}

		
	}

	function ImageValidation(){
		// Image validations:
		// 1) only 'png' and 'jpg'
		// 2) file does not already exist
		// *** NOTE *** ===> IMAGE IS NOT REQUIRED on the "Edit My Profile" page
		$target_dir = "C:/xampp/htdocs/socialnetwork/Images/";
		$target_file = $target_dir . basename($_FILES["fileImage"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$error = "";

		// Skip validation if image was not selected
		// Check validation if user selected an image
		if (!($_FILES["fileImage"]["tmp_name"] == "")){
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
				$error = "* Only JPG, JPEG, and PNG files are allowed";
			}else if (file_exists($target_file)) {
				$error = "* File already exists";
			}
		}
		return $error;
	}
	function NameValidation(){
		// Name validations:
		// 1) required
		// 2) less than 51 characters
		// 3) only letters and numbers allowed
		$error = "";
		if (empty($_POST["txtName"])){
			$error = "* Name is required";
		}else if (strlen($_POST["txtName"]) > 50){
			$error = "* Name must be less than 51 characters";
		}else if (!preg_match("/^[a-zA-Z ]*$/", $_POST["txtName"])){
			$error = "* Name must contain only letters";
		}
		return $error;		
	}
	function AboutMeValidation(){
		// AboutMe validations:
		// 1) required
		$error = "";
		if (empty($_POST["areaAboutMe"])){
			$error = "* About Me is required";
		}else if (strlen($_POST["areaAboutMe"]) > 10000){
			$error = "* About Me must be less than 10,000 characters";
		}
		return $error;
	}
	
	
	// This function is used to trim white space
	// and encode html special characters.
	function test_input($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data, ENT_QUOTES);
		return $data;
	}
?>

<div class="container">

	<?php include 'navbar.php' ?>
	
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h3>Edit My Profile</h3>
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
					<label for="txtName">Name:</label>
					<input class="form-control" value="<?php echo $name ?>" type="text" name="txtName" id="txtName" >
					<span class="text-danger"><?php echo $nameError ?></span>							
				</div>
	
				<div class="form-group">
					<label for="areaAboutMe">About Me:</label>
					<textarea class="form-control" rows="5" id="areaAboutMe" name="areaAboutMe"><?php echo $aboutMe ?></textarea>
					<span class="text-danger"><?php echo $aboutMeError ?></span>
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
				<div class="form-group">
					<label>Current Image:</label>
					<img class="img-responsive" src="<?php echo $location ?>" />
				</div>
				<div class="form-group">
					<label class="btn btn-primary btn-file" for="fileImage">
						Change Image
						<input class="form-control" type="file" name="fileImage" id="fileImage" style="display:none;">
					</label>							
					<span class="text-danger"><?php echo $imageError ?></span>
				</div>
			</div>		
		</div>
		<div class="row">		
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<button id="submit" name="submit" type="submit" class="btn btn-primary">Update Profile</button><br>
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