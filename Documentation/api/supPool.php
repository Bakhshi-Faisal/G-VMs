<?php

// suppression de pool disk
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];




switch($request_method)
  {
    case 'PUT':
      
        $disk = $_GET["diskdel"];

        
        supPool($disk);
           
        
      break;
    default:

  }
   function supPool($disk)
  {
   
    global $lib;
    
    $_PUT = array(); 

    if(!empty($disk))
    {

        parse_str(file_get_contents('php://input'), $_PUT);

  
         $lib->supprimer_vol($disk);
     
     
        $lib->recharger_vol("default");

        $reponse = "Pool a été bien supprimé";

    }else
    {
        $reponse = "erreur est survenue";
    }
    
    header('Content-Type: application/json');
    echo json_encode($reponse, JSON_PRETTY_PRINT);

  }
 

  
  




















?>