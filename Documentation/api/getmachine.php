<?php 
// affichage d'une machine

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];


function gettoutemachine(){

    global $lib;
  
    $domin = $lib->get_domains();

      header('Content-Type: application/json');
      echo json_encode($domin, JSON_PRETTY_PRINT);

  }

  function get_machine_by_uuid($uuid) {

    global $lib;

  $dom =  $lib->domain_get_name_by_uuid($uuid);


    $rslt = $lib->get_one_domain($dom);


    header('Content-Type: application/json');
    echo json_encode($rslt, JSON_PRETTY_PRINT);

    
}


switch($request_method)
  {
    case 'GET':
      if(!empty($_GET["uuid"]))
      {
       
        $id = $_GET["uuid"];
        get_machine_by_uuid($id);
      }
      else
      {
       
        gettoutemachine();
        
      }
      break;
    default:

  }




?>