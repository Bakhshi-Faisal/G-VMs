<?php

// on affiche les états de chaque machine.
// on change les états des machines


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
      
        $id = $_GET["uuid"];
       
        
        changeEtatMachine($id);
        
      break;
    default:

  }
  function changeEtatMachine($id)
  {

    $action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

    global $lib;
    
    $_PUT = array(); 

  
    parse_str(file_get_contents('php://input'), $_PUT);

    $action = $_PUT["action"];
  
    if ($action) {
        
      $domName = $lib->domain_get_name_by_uuid($id);
  
      if ($action == 'demarrer-machine') {
       $reponse =  $lib->demarrer($domName) ? array('status' 
       => 1, 'status_message' => "La machine a été démarrée avec succès" ): array('status' 
       => 0, 'status_message' => 'Erreur lors du démarrage de la machine: '.$lib->get_last_error());
      } else if ($action == 'arreter-machine') {
       $response =   $lib->eteint($domName) ? "La machine a été arrêtée avec succès" : 'Erreur lors de larrêt de la machine: '.$lib->get_last_error();
      }  else if ($action == 'pause-machine') {
        $lib->suspendre($domName) ? "La machine a été suspendu avec succès" : 'Erreur lors de la suspension de la machine: '.$lib->get_last_error();
      }
      else if ($action == 'reprendre-machine') {
         $lib->reprendre($domName) ? "La machine a été repris avec succès" : 'Erreur lors de la reprise de la demaine: '.$lib->get_last_error();
      }
      else if ($action == 'delete-machine')
      {
       $response = $lib->domain_undefine($domName) ? "La machine a été supprimé avec succès" : 'Erreur lors de la suppression de la demaine: '.$lib->get_last_error();
      }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);


  }


  
  




















?>