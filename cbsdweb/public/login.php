<?php
date_default_timezone_set("Europe/Moscow");

$data = false;

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);

	function registration_callback($username, $email, $userdir)
	{
		// all it does is bind registration data in a global array,
		// which is echoed on the page after a registration
		global $data;
		$data = array($username, $email, $userdir);
	}

	require_once("user.php");
	$USER = new User("registration_callback");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login to CP</title>
		<meta charset="utf-8"/>
		<script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/user.js"></script>
		<link rel="stylesheet" type="text/css" href="css/reg.css"></link>
	</head>

	<body>
		<h1>Welcome to CP</h1>

		<?php if($USER->error!="") { ?>
		<p class="error">Error: <?php echo $USER->error; ?></p>
		<?php } ?>

		<table style="width: 100%; margin-top: 1em;"><tr><td style="width: 24em; padding-top:1em;">

<?php
if(!$USER->authenticated) { ?>

                        <!-- Allow a user to log in -->
                        <form class="controlbox" name="log in" id="login" action="login.php" method="POST">
                                <input type="hidden" name="op" value="login"/>
                                <input type="hidden" name="sha1" value=""/>
                                <table>
                                        <tr><td>user name </td><td><input type="text" name="username" value="" /></td></tr>
                                        <tr><td>password </td><td><input type="password" name="password1" value="" /></td></tr>
                                </table>
                                <br><br>
                                <input type="button" value="log in" onclick="User.processLogin()"/>
                                <br>
                        </form>
<?php           } else {

	header( 'Location: index.php' ) ;

}

			if($USER->authenticated) { ?>

			<!-- Log out option -->
			<form class="controlbox" name="log out" id="logout" action="login.php" method="POST">
				<input type="hidden" name="op" value="logout"/>
				<input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" />
				<p>You are logged in as <?php echo $_SESSION["username"]; ?></p>
				<input type="submit" value="log out"/>
			</form>
<?php 		}

			if($USER->authenticated) { ?>

			<!-- If a user is logged in, her or she can modify their email and password -->
			<form class="controlbox" name="update" id="update" action="login.php" method="POST">
				<input type="hidden" name="op" value="update"/>
				<input type="hidden" name="sha1" value=""/>
				<p>Update your email address and/or password here</p>
				<table>
					<tr><td>email address </td><td><input type="text" name="email" value="<?php $USER->email; ?>" /></td></tr>
					<tr><td>new password </td><td><input type="password" name="password1" value="" /></td></tr>
					<tr><td>new password (again) </td><td><input type="password" name="password2" value="" /></td></tr>
				</table>
				<input type="button" value="update" onclick="User.processUpdate()"/>
			</form>
<?php 		}

			if($USER->authenticated) { ?>

			<!-- If a user is logged in, they can elect to unregister -->
			<form class="controlbox" name="unregister" id="unregister" action="login.php" method="POST">
				<input type="hidden" name="op" value="unregister"/>
				<input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" />
				<p>To unregister, press the button...</p>
				<input type="submit" value="unregister"/>
			</form>
<?php 		} ?>

		</td><td style="padding-left: 4em;">
		</td></tr><table>

	</body>
</html>