<?php 


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];


function gettoutemachines(){

    global $lib;
  
    $domin = $lib->getinfosToutMachine();

      header('Content-Type: application/json');
      echo json_encode($domin, JSON_PRETTY_PRINT);

  }

  


switch($request_method)
  {
    case 'GET':


        gettoutemachines();
      
      break;
    default:

  }




?>