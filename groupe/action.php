<?php

//action.php

include('database_connection.php');

if(isset($_POST["action"]))
{
	$vms= implode(",", $_POST["vms"]);
	$data = array(
		':groupe' => $_POST["groupe"],
		':vms'	=>	$vms
	);
	$query = '';
	if($_POST["action"] == "insert")
	{
		$query = "INSERT INTO machines (groupe, vms) VALUES (:groupe, :vms)";
	}
	if($_POST["action"] == "edit")
	{
		$query = "
		UPDATE machines 
		SET groupe = :groupe, 
		vms = :vms 
		WHERE id = '".$_POST['hidden_id']."'
		";
	}

	$statement = $connect->prepare($query);
	$statement->execute($data);
}


?>