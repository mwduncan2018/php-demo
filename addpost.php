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
	$text = "";
	$textError = "";
	$submitSuccessError = "";

	// Step 2: If "Http-Post"
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		// Remove white space and encode special chars
		$text = test_input($_POST["txtText"]);

		// Validations
		$textError = textValidation();

		// If there are no validation errors...
		if ($textError == ""){

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

			// Insert POST
			$db_sql = "INSERT INTO Post (Text, UserId)
			VALUES ('" . $text . "' ,'" . $_SESSION['userId'] . "' )";
			if (mysqli_query($db_conn, $db_sql)){
				$submitSuccessError = "New post created successfully";
				$text = "";
			}else{
				$submitSuccessError = "Error: " . $db_sql . "<br>" . mysqli_error($db_conn);
			}					
		
		}	
	}

	function textValidation(){
		// Text validations:
		// 1) required
		// 2) must be less than 10,000 chars
		$error = "";
		if (empty($_POST["txtText"])){
			$error = "* Text is required";
		}else if (strlen($_POST["txtText"]) > 10000){
			$error = "* Text must be less than 10,000 characters";
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
			<h3>Add Post</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<hr>
		</div>
	</div>
	
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" >

		<div class="row">		
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="form-group">
					<label for="txtName">Text:</label>
					<textarea class="form-control" rows="5" id="txtText" name="txtText"><?php echo $text ?></textarea>
					<span class="text-danger"><?php echo $textError ?></span>							
				</div>
			
				<div class="form-group">
					<button id="submit" name="submit" type="submit" class="btn btn-primary">Submit Post</button><br>
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