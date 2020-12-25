<?php
// récupération de fichier xml de réseau

require('../mainClass/libvirt.php');

$lib = new Libvirt();

$connd = $lib->connexion("qemu:///system", false);

$request_method = $_SERVER["REQUEST_METHOD"];


switch ($request_method) {
    case 'GET':

        reseau("default");

        break;
    default:

        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
function reseau($network_name)
{
    global $lib;
    if ($data = $lib->get_network_xml($network_name)) {
        header("Content-type: text/xml");
        die($data);
    }
}
