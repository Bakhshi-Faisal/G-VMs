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
        $nom = $_POST["nomr"];
        changeEtatReseau($nom);
        break;
    default:

}
function changeEtatReseau($nom)
{

    $action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

    global $lib;

    $_PUT = array();



    parse_str(file_get_contents('php://input'), $_PUT);

    $action = $_PUT["action"];
    $name = $_GET['nomr'];
    $ret = false;

    if ($action) {

        if ($action == 'demarrer-reseau') {
            $response = $lib->activer_reseau($name, true) ? "Le réseau a été démarré avec succès" : 'Erreur lors du démarrage du réseau: '.$lib->get_last_error();
        } else if ($action == 'arreter-reseau') {
            $response = $lib->activer_reseau($name, false) ? "Le réseau a été arrêté avec succès" : 'Erreur lors de l\' arrêt du réseau: '.$lib->get_last_error();
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);


}
























?>