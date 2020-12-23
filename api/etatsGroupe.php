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

        $vms = str_replace('%20',' ',$_GET['vms']);


        changeEtatGroupe($vms);

        break;
    default:

}
function changeEtatGroupe($vms)
{

    $action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

    global $lib;

    $_PUT = array();
    var_dump($_PUT);

    $vms = $_GET['vms'];
    $myArray = explode(',', $vms);

    var_dump($myArray);

    parse_str(file_get_contents('php://input'), $_PUT);

    $action = $_PUT["action"];



    if ($action) {

        if ($action == 'demarrer-groupe') {

            foreach ($myArray as $nom) {
                $etat = $lib->demarrer($nom);
            }
        }
        else if ($action == 'pause-groupe')
        {

            foreach ($myArray as $nom) {
                $etat = $lib->suspendre($nom);
            }
        }
        else if ($action == 'arreter-groupe')
        {

            foreach ($myArray as $nom) {
                $etat = $lib->eteint($nom);
            }
        }
        else if ($action == 'reprendre-groupe')
        {

            foreach ($myArray as $nom) {
                $etat = $lib->reprendre($nom);
            }
        }

    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);


}
























?>