<?php
// On fait un appel à fonction network_change_xml pour distribuer des IP fixes aux machines
// on utilise la méthode POST pour pour modifier le fichier XML


error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);


require('../mainClass/libvirt.php');

$lib = new Libvirt();

$conn=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];




switch($request_method)
{
    case 'POST':

            $nom = "default";
            modifierXml($nom);



        break;
    default:

}
function modifierXml($network_name)
{
    global $lib;
    $_PUT = array();
    parse_str(file_get_contents('php://input'), $_PUT);
    $xml = $_PUT['editXml'];

    if(  $lib->network_change_xml($network_name, $xml) ){
        $response= "Le réseau a été modifiée (vous devrez peut-être démarrer / arrêter le réseau";
    } else
    {
        $response=   'Error changing network definition: ' . $lib->get_last_error();
    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}
