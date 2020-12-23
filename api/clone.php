<?php 
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
      
        $disk = $_GET["disk"];
        


     
       clonePool($disk);
           
        
      break;
    default:

  }
   function clonePool($disk)
  {

    var_dump($disk);
   
    global $lib;
    
    $_PUT = array(); 

  
    parse_str(file_get_contents('php://input'), $_PUT);

  
     $nom=$_GET['nom'];

     if(!empty($nom))
     {
     
  
     while(file_exists('/var/lib/libvirt/images/'.$nom)){
        $nom= "clone-".$nom;
      
    }
        

         $file = $disk;
         $newfile = '/var/lib/libvirt/images/'.$nom;

        exec("cp '$file' '$newfile'", $output, $retrun_var);

        var_dump($retrun_var);
        var_dump($output);
 
 
        $lib->recharger_vol("default");

        
        $reponse ="le disk a bien été cloné";

  }
  else{
    $reponse ="Erreur est survenue";
  }
    header('Content-Type: application/json');
    echo json_encode($reponse, JSON_PRETTY_PRINT);

  }
 

  
  




















?>