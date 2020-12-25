<?php 
// récupéraion de Pool Disk
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];



switch($request_method)
  {
    case 'GET':
      
       
        getVolumes("default");
        
      break;
    default:

  }
  function getVolumes($pool)
{
    global $lib;
    $conn=libvirt_connect("qemu:///system",false);
    $response  = array();
    $v = 0;


    $t=libvirt_list_storagepools($conn);

   
    for ($i = 0; $i < sizeof($t); $i++)
       {	
           
        
        $pname=$t[$i];$nvol="";$state="";
         
 
     $pres=libvirt_storagepool_lookup_by_name($conn, $pname);
     $x=libvirt_storagepool_list_volumes($pres);
 
 
     foreach($x as $value)
     {
        $volumes = libvirt_storagevolume_lookup_by_name($pres, $value);
       
        $response[$v]["path_vol"] = libvirt_storagevolume_get_path($volumes);
        $response[$v]["nom_vol"] = libvirt_storagevolume_get_name($volumes);
         
 
        
     $v++;
     }
 
       }
      
        
    header('Content-Type: application/json');
    echo  json_encode($response, JSON_PRETTY_PRINT);


  }


  
  