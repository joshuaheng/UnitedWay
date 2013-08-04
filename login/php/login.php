<?php
	/**
	 * php/Favorites.php
	 * Author: Henry Lin (Minor additions: Paul Kim, update with PHPASS encryption)
	 * PHPASS: http://www.openwall.com/phpass/
	 * PHP script used by :
	 *     login/js/registration.js
	 *
	 * Queries from tables: users
	 * Updates tables: users
	 */
	 
	require("../../php/connect.php");
	require("../../php/phpass-0.3/PasswordHash.php");
	// Prevent Cross Site Scripting Attacks
    $user_email = strip_tags(get_post_var('pUser'));
    $user_pass = strip_tags(get_post_var('pPass'));
	
	// Base-2 logarithm of the iteration count used for password stretching
	$hash_cost_log2 = 8;
	// Do we require the hashes to be portable to older systems (less secure)?
	$hash_portable = FALSE;
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
	$hash = $hasher->HashPassword($user_pass);
	unset($hasher);
	
	// Prevent SQL injections with prepared statements
	// mySQL connection variable: $dbConnection 
    $userQuery = $dbConnection ->prepare("SELECT * FROM users WHERE user_email= ? AND user_password= ?");
    $userQuery->bind_param('ss', $user_email, $hash);
    if ($userQuery->execute()){
        $userQuery->store_result();
        $userQuery->bind_result($column1, $column2, $column3, $column4, $column5, $column6, $column7); // We want to give the user $column1
		$userQuery->fetch();
        if ($userQuery->num_rows == 1) {
			// Successful login, echo out parentID.
			$retStr = "SUCCESS:$column1";
			echo $retStr;
        }
        else {
			// Unsuccessful Login.
            echo "FAIL";
        }
    } else echo "SERVER_FAIL"; 
	
	mysqli_close($dbConnection);

	function get_post_var($var) {
		$val = $_POST[$var];
		if (get_magic_quotes_gpc())
			$val = stripslashes($val);
		return $val;
	}
?>
