<?php

include("db/connexion.php");
require('../mainClass/libvirt.php');

$lib = new Libvirt();

$connd=$lib->connexion("qemu:///system",false);

$request_method = $_SERVER["REQUEST_METHOD"];


switch($request_method)
{
    case 'GET':

            getGroupes();

        break;
    default:

        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
 function getGroupes()
{
    global $conn ,$lib;
    $query = "SELECT groupe, vms FROM `machines`";
    $response = array();
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_array($result))
    {
        $response[] = $row;
    }

    $nom_vms = array();
    $etats = array();

    for($i = 0; $i < count($response);$i++) {

       $ar =explode(",",$response[$i]['vms']);
       $nom_vms[$i]=$ar;

        foreach ($nom_vms[$i] as $nom)
        {
            $arr = explode("\n", $nom);


            $etat = $lib->get_domain_etat($nom);
            $etats[]=$etat;

        }
    }



header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

}