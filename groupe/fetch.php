
<div class="row">
    <div class="col-md-4">

        <?php
        ini_set('display_startup_errors',1);
        ini_set('display_errors',1);
        error_reporting(-1);

        $response = httpGet('http://127.0.0.1/api/getmachine.php');
        $response = json_decode($response, true);

        function httpGet($url)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);
            curl_close($ch);
            return $output;
        }


        echo '<ul class="list-group ">';
            echo' <h2 class="font-weight-bold">Machines Virtuelle</h2>';
        foreach ($response as $m) {

            echo '<li  style="
    font-size: 15px;
" class="list-group-item text-dark">'.$m["name"].'</li>';

        }
        echo '</ul>'


        ?>




    </div>
    <div class="col-md-8"><?php



        include('database_connection.php');

        $query = "SELECT * FROM machines ORDER BY id DESC";

        $statement = $connect->prepare($query);

        $statement->execute();

        $result = $statement->fetchAll();

        $total_rows = $statement->rowCount();

        $output = '

<div class=" shadow-lg p-3 mb-5 rounded">

<div class="row">

<table class="table  mt-5 table-responsive">
	<thead class="text-info text-center">
		<tr>
			<th>Groupe</th>
			<th>Machines Virtuelle</th>
			<th>Modifier</th>
			<th>Supprimer</th>
		</tr>
	</thead>


</div>
	
';

        if($total_rows > 0)
        {
            foreach($result as $row)
            {
                $output .= '


		<tr>
			<td>'.$row["groupe"].'</td>
			<td>'.$row["vms"].'</td>
			<td><button type="button" name="edit" id="'.$row["id"].'" class="btn btn-warning btn-xs edit">Modifier</button></td>
			<td><button type="button" name="delete" id="'.$row["id"].'" class="btn btn-danger btn-xs delete">Supprimer</button></td>
		</tr>
		';
            }
        }
        else
        {
            $output .= '
	<tr>
		<td colspan="4">Aucune donnée disponible</td>
	</tr>
	';
        }
        $output .= '</table></div>';

        echo $output;

        ?></div>

</div>
