

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php
				if (isset($_SESSION['username'])){
					echo "<p class='pull-right'>Logged in as " . $_SESSION['username'] . "</p>";
				}else{
					echo "<p class='pull-right'>Not logged in</p>";
				}



			?>
		</div>
	</div>
	<nav class='navbar navbar-inverse'>
		<div class='container-fluid'>
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#myNavbar'>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
				</button>
				<a name='linkMikeBook' class='navbar-brand' href='/socialnetwork/default.php/'>Mike-Book</a>
			</div>
			<div class='collapse navbar-collapse' id='myNavbar'>
				<ul class='nav navbar-nav'>
					<li class='dropdown'>
						<a name='linkProfile' class='dropdown-toggle' data-toggle='dropdown' href='#'>Profile<span class='caret'></span></a>
						<ul class='dropdown-menu'>
							<li><a name='linkViewMyProfile' href='/socialnetwork/viewmyprofile.php/'>View My Profile</a></li>
							<li><a name='linkEditMyProfile' href='/socialnetwork/editmyprofile.php/'>Edit My Profile</a></li>
							<li><a name='linkDeleteMyProfile' href='/socialnetwork/deletemyprofile.php/'>Delete My Profile</a></li>							
						</ul>
					</li>
					<li class='dropdown'>
						<a name='linkPosts' class='dropdown-toggle' data-toggle='dropdown' href='#'>Posts<span class='caret'></span></a>
						<ul class='dropdown-menu'>
							<li><a name='linkViewMyPosts' href='/socialnetwork/viewmyposts.php/'>View My Posts</a></li>
							<li><a name='linkAddPost' href='/socialnetwork/addpost.php/'>Add Post</a></li>
							<li><a name='linkRemovePost' href='/socialnetwork/removepost.php/'>Remove Post</a></li>
						</ul>
					</li>
					<li><a name='linkSearchUsers' href='/socialnetwork/searchusers.php/'>Search Users</a></li>
					<li><a name='linkFeed' href='/socialnetwork/feed.php/'>Feed</a></li>
				</ul>
				<ul class='nav navbar-nav navbar-right'>
				
					<?php
						if (isset($_SESSION['username'])){
							echo "<li><a name='linkLogout' href='/socialnetwork/logout.php/'><span class='glyphicon glyphicon-log-out'></span> Logout</a></li>";							
						}else{
							echo "<li><a name='linkRegister' href='/socialnetwork/register.php/'><span class='glyphicon glyphicon-user'></span> Register</a></li>";
							echo "<li><a name='linkLogin' href='/socialnetwork/login.php/'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
						}				
					?>					

				</ul>
			</div>
		</div>
	</nav>
	
