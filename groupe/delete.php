<?php

//delete.php

include('database_connection.php');

if(isset($_POST["id"]))
{
	$query = "DELETE FROM machines WHERE id = '".$_POST['id']."'";
	$statement = $connect->prepare($query);
	$statement->execute();
}


?>