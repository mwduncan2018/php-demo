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
	// Define Page Variable(s)
	$webmaster = "Mike Duncan";
	$name = "";
	$nameError = "";
	$username = "";
	$usernameError = "";
	$password = "";
	$passwordError = "";
	$confirmPassword = "";
	$confirmPasswordError = "";
	$image;
	$imageError = "";
	$aboutMe = "";
	$aboutMeError = "";
	$submitSuccessError = "";
	
	// If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		// Remove white space and encode special chars
		$name = test_input($_POST["txtName"]);
		$username = test_input($_POST["txtUsername"]);			
		$password = test_input($_POST["txtPassword"]);
		$confirmPassword = test_input($_POST["txtConfirmPassword"]);
		$aboutMe = test_input($_POST["areaAboutMe"]);
		
		// Validate fields
		$nameError = NameValidation();
		$usernameError = UsernameValidation();
		$passwordError = PasswordValidation();
		$confirmPasswordError = ConfirmPasswordValidation();
		$imageError = ImageValidation();
		$aboutMeError = AboutMeValidation();
		
		// If no validation errors...
		if ($nameError == "" && $usernameError == "" && $passwordError == "" && $confirmPasswordError == "" && $imageError == "" && $aboutMeError == ""){
		
			// Upload the image file to the server
			// If there is an error while uploading, display a message and DO NOT save the record to the "socialnetworkdb1" database
			$target_dir = "C:/xampp/htdocs/socialnetwork/Images/";
			$target_name = basename($_FILES["fileImage"]["name"]);
			$target_file = $target_dir . $target_name;
			if (!(move_uploaded_file($_FILES["fileImage"]["tmp_name"], $target_file))) {
				$submitSuccessError = "Sorry, there was an error uploading your file";
			}
			// else, INSERT the image and the user into the "socialnetworkdb1" database
			else{

				// Define DB Variables
				$db_server = "localhost";
				$db_username = "root";
				$db_password = "";
				$db_database = "socialnetworkdb1";
				$db_last_id = -1; // UserId of the last inserted user
				
				// Create DB Connection
				$db_conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
				if (!$db_conn){
					die("Connection Failed: " . mysqli_connect_error());
				}
				
				// INSERT user
				$db_sql = "INSERT INTO User (Name, UserName, Password, AboutMe)
							VALUES ('" . $name . "' ,'" . $username . "' ,'" . $password . "' ,'" . $aboutMe . "' )";
				if (mysqli_query($db_conn, $db_sql)){
					$db_last_id = mysqli_insert_id($db_conn);
				}else{
					$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
				}

				// If inserting the user was successful, INSERT image
				if ($db_last_id != -1){
					$db_sql = "INSERT INTO Image (Location, UserId)
								VALUES ('/socialnetwork/Images/" . $target_name . "', '" . $db_last_id . "' )";
					// If inserting the image was successful, INSERT a row into the FOLLOW table so this newly created user follows their own posts
					if (mysqli_query($db_conn, $db_sql)){
						$db_sql = "INSERT INTO follow (UserId, UserFollowed)
									VALUES ('" . $db_last_id . "' ,'" . $db_last_id . "');";
						if (mysqli_query($db_conn, $db_sql)){
							$submitSuccessError = "New record created successfully";
							$name = "";
							$username = "";
							$password = "";
							$confirmPassword = "";
							$aboutMe = "";
						}else{
							$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
						}
					}else{
						$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
					}					
				}
				
				// Close DB Connection
				mysqli_close($db_conn);
			
			}
			
		
		}
	}

	function ImageValidation(){
		// Image validations:
		// 1) required
		// 2) only 'png' and 'jpg'
		// 3) file does not already exist
		$target_dir = "C:/xampp/htdocs/socialnetwork/Images/";
		$target_file = $target_dir . basename($_FILES["fileImage"]["name"]);
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$error = "";
		
		if ($_FILES["fileImage"]["tmp_name"] == ""){
			$error = "* Image is required";
		}else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
			$error = "* Only JPG, JPEG, and PNG files are allowed";
		}else if (file_exists($target_file)) {
			$error = "* File already exists";
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
	function UsernameValidation(){
		// Username validations:
		// 1) required
		// 2) less than 51 characters
		// 3) only letters and numbers allowed
		// 4) cannot already exist in DB
		$error = "";
		if (empty($_POST["txtUsername"])){
			$error = "* Username is required";
		}else if (strlen($_POST["txtUsername"]) > 50){
			$error = "* Username must be less than 51 characters";
		}else if (!preg_match("/^[a-zA-Z]*$/", $_POST["txtUsername"])){
			$error = "* Username must contain only letters";
		}else{
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

			// Search if this Username already exists in DB
			$db_sql = "SELECT UserName FROM User WHERE UserName = '" . $_POST["txtUsername"] . "'";
			$db_result = mysqli_query($db_conn, $db_sql);
			$db_rowCount = mysqli_num_rows($db_result);
			if ($db_rowCount > 0){
				$error = "* Username cannot already exist";
			}
			
			// Close DB Connection
			mysqli_close($db_conn);			
		}
		return $error;
	}
	function PasswordValidation(){
		// Password validations:
		// 1) required
		// 2) less than 51 characters
		// 3) only letters and numbers allowed
		$error = "";
		if (empty($_POST["txtPassword"])){
			$error = "* Password is required";
		}else if (strlen($_POST["txtPassword"]) > 50){
			$error = "* Password must be less than 51 characters";			
		}else if (!preg_match("/^[a-zA-Z]*$/", $_POST["txtPassword"])){
			$error = "* Password must contain only letters";
		}
		return $error;
	}
	function ConfirmPasswordValidation(){
		// ConfirmPassword validations:
		// 1) required
		// 2) must match Password field
		$error = "";
		if (empty($_POST["txtConfirmPassword"])){
			$error = "* Confirm Password is required";
		}else if (($_POST["txtConfirmPassword"]) != ($_POST["txtPassword"])){
			$error = "* Confirm Password and Password must match";			
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
			<h3>Register</h3>
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
					<label for="txtName">Name:</label>
					<input class="form-control" value="<?php echo $name ?>" type="text" name="txtName" id="txtName" >
					<span class="text-danger"><?php echo $nameError ?></span>							
				</div>

				<div class="form-group">
					<label for="txtUsername">Username:</label>
					<input class="form-control" value="<?php echo $username ?>" type="text" name="txtUsername" id="txtUsername" >
					<span class="text-danger"><?php echo $usernameError ?></span>
				</div>

				<div class="form-group">
					<label for="txtPassword">Password:</label>
					<input class="form-control" value="<?php echo $password ?>" type="password" name="txtPassword" id="txtPassword" >
					<span class="text-danger"><?php echo $passwordError ?></span>
				</div>
				
				<div class="form-group">
					<label for="txtConfirmPassword">Confirm Password:</label>
					<input class="form-control" value="<?php echo $confirmPassword ?>" type="password" name="txtConfirmPassword" id="txtConfirmPassword" >
					<span class="text-danger"><?php echo $confirmPasswordError ?></span>
				</div>
		
				<div class="form-group">
					<label for="areaAboutMe">About Me:</label>
					<textarea class="form-control" rows="5" id="areaAboutMe" name="areaAboutMe"><?php echo $aboutMe ?></textarea>
					<span class="text-danger"><?php echo $aboutMeError ?></span>
				</div>
				
				<div class="form-group">
					<label class="btn btn-primary btn-file" for="fileImage">
						Select Image
						<input class="form-control" type="file" name="fileImage" id="fileImage" style="display:none;">
					</label>							
					<span class="text-danger"><?php echo $imageError ?></span>
				</div>
				
				<div>
					<button id="submit" name="submit" type="submit" class="btn btn-primary">Register</button>
					<span class="text-success"><?php echo $submitSuccessError ?></span>						
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