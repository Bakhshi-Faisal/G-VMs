
<?php 
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];

function gethost(){

    global $lib;
  
    $domin = $lib->gethostinfo();

      header('Content-Type: application/json');
      echo json_encode($domin, JSON_PRETTY_PRINT);

  }

  switch($request_method)
  {
    case 'GET':


        gethost();
      
      break;
    default:

  }