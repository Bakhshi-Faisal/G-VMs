<?php

//select.php

include('database_connection.php');

if(isset($_POST["id"]))
{
	$query = "SELECT * FROM machines WHERE id='".$_POST["id"]."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$vms = '';
	$groupe = '';
	foreach($result as $row)
	{
        $groupe = $row["groupe"];
		$vms_array = explode(",", $row["vms"]);
		$count = 1;
		foreach($vms_array as $vm)
		{
			$button = '';
			if($count > 1)
			{
				$button = '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">x</button>';
			}
			else
			{
				$button = '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			$vms .= '
				<tr id="row'.$count.'">
					<td><input type="text" name="vms[]" placeholder="Nom de la machine" class="form-control name_list" value="'.$vm.'" /></td>
					<td align="center">'.$button.'</td>
				</tr>
			';
			$count++;
		}
	}
	$output = array(
		'groupe'					=>	$groupe,
		'vms'	=>	$vms
	);
	echo json_encode($output);
}


?>