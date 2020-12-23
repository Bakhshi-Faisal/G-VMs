<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="../style.css">
    <title>VM Manager</title>



  </head>
  <body>
<?php include'../menu.php' ?>

 <div class="container">
 <h3 class="text-center display-4 mb-5">Gestion de Groupe </h3>

     <div class="row">
         <div class="col-md-4">
             <?php
             $groupe = httpGet('http://127.0.0.1/api/getVmGroupe.php');
             $groupe = json_decode($groupe, true);
             foreach ($groupe as $m) {

                 echo ' <li class="list-group-item d-flex justify-content-between align-items-center">
                        '.$m['name'].'
                        <span class="badge badge-primary badge-pill">'.$m['etat'].'</span>
                    </li>';


             }

             ?>
         </div>
         <div class="col-md-8">
             <?php

             ini_set('display_startup_errors',1);
             ini_set('display_errors',1);
             error_reporting(-1);

             $response = httpGet('http://127.0.0.1/api/getGroupes.php');
             $response = json_decode($response, true);
             echo '<div class="row">';
             foreach ($response as $m) {
                 echo '
                 <div class="card text-white bg-info m-3" style="max-width: 18rem;">
  <div class="card-header">'.$m['groupe'].'</div>
  <div class="card-body">
    <h5 class="card-title">'.$m['vms'].'</h5>
    <p class="card-text">
    

    <a class="btn btn-primary m-2" href="?action=demarrer-groupe&vms='. $m["vms"] .'"> Démarrer  </a>
    <a class="btn btn-primary m-2" href="?action=arreter-groupe&vms='. $m["vms"] .'"> Arreter  </a>
    <a class="btn btn-primary m-2" href="?action=pause-groupe&vms='. $m["vms"] .'"> Pause  </a>
    <a class="btn btn-primary m-2" href="?action=reprendre-groupe&vms='. $m["vms"] .'"> Reprendre  </a>


    
    
    </p>
  </div>
</div>




'; }

echo '</div>';




             $action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

             if ($action) {
                 $_GET['vms'] = str_replace('%20',' ',$_GET['vms']);
                 
                 $url = "http://localhost/api/etatsGroupe.php?vms=" . $_GET['vms'];
                 $data = array('action' => $_GET['action']);

                 $response = httpPut($url, $data);
                 $response = json_decode($response, true);
                 if(!empty($response)){
                    echo $response;
                 }
             }

             function httpGet($url)
             {
                 $ch = curl_init();

                 curl_setopt($ch, CURLOPT_URL, $url);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                 $output = curl_exec($ch);
                 curl_close($ch);
                 return $output;
             }

             function httpPut($url, $params)
             {
                 $ch = curl_init($url);
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                 $output = curl_exec($ch);
                 curl_close($ch);
                 return $output;
             }






             ?>

         </div>






</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
