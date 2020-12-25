<?php

class Libvirt {
    private $conn;
    private $last_error;
    private $allow_cached = true;
    private $dominfos = array();


    /**
     * Libvirt constructor.
     * @param false $debug
     */
    function __construct($debug = false) {
        if ($debug)
            $this->set_logfile($debug);
    }

    /**
     * @return false
     * Cette fonction est utilisée pour obtenir la dernière erreur provenant de libvirt ou de l'extension PHP elle-même.
     * Renvoie: dernière erreur
     */
    function _set_last_error() {
        $this->last_error = libvirt_get_last_error();
        return false;
    }

    /**
     * @param $filename
     * @return bool
     * Fonction pour définir le fichier log de l'instance du module libvirt.
     * Return : TRUE si le fichier log a été défini avec succès, sinon FALSE
     */
    function set_logfile($filename) {
        if (!libvirt_logfile_set($filename))
            return $this->_set_last_error();

        return true;
    }


    /**
     * @param string $uri
     * @return bool
     * fonction est utilisé pour se connecter au daemon libvirt spécifié à l'aide de l'URL spécifiée,
     * l'utilisateur peut également définir l'indicateurde lecture seule et / ou définir les informations d'identification pour la connexion.
     * Return : ressource de connexion libvirt
     */
    function connexion($uri = 'null') {
        $this->conn=libvirt_connect($uri, false);
        if ($this->conn==false)
            return $this->_set_last_error();
        return true;
    }

    /**
     * @param $nameRes
     * @return false|resource
     * La fonction est utilisée pour obtenir la ressource de domaine
     */
    function objet_domaine($nameRes) {
        if (is_resource($nameRes))
            return $nameRes;

        $dom=@libvirt_domain_lookup_by_name($this->conn, $nameRes);
        if (!$dom) {
            $dom=@libvirt_domain_lookup_by_uuid_string($this->conn, $nameRes);
            if (!$dom)
                return $this->_set_last_error();
        }

        return $dom;
    }


    /**
     * @param $state
     * @return string
     * La fonction renvoie l'état du domaine.
     */
    function etat($state) {
        switch ($state) {
        case VIR_DOMAIN_RUNNING:  return 'En cours';
        case VIR_DOMAIN_NOSTATE:  return 'Pas d\'etat';
        case VIR_DOMAIN_BLOCKED:  return 'Bloquée';
        case VIR_DOMAIN_PAUSED:   return 'En pause';
        case VIR_DOMAIN_SHUTDOWN: return 'Shutdown';
        case VIR_DOMAIN_SHUTOFF:  return 'Shutoff';
        case VIR_DOMAIN_CRASHED:  return 'Crashed';
        }

        return 'unknown';
   }

    /**
     * @param $dom
     * La fonction est utilisée pour obtenir les informations du domaine.
     * array informations de domaine
     *
     * @return mixed
     */
    function domain_get_info($dom) {
    if (!$this->allow_cached)
        return libvirt_domain_get_info($dom);

    $domname = libvirt_domain_get_name($dom);
    if (!array_key_exists($domname, $this->dominfos)) {
        $info = libvirt_domain_get_info($dom);
        $this->dominfos[$domname] = $info;
    }

    return $this->dominfos[$domname];
}


    /**
     * @return mixed
     */
    function get_last_error() {
      return $this->last_error;
    }


    /**
     * @param $uuid
     * @return false
     * La fonction est utilisée pour obtenir le domaine par son UUID accepté au format chaîne.
     * renvoie: ressource de domaine libvirt
     */
    function domain_get_name_by_uuid($uuid) {
    $dom = libvirt_domain_lookup_by_uuid_string($this->conn, $uuid);
    if (!$dom)
        return false;
    $tmp = libvirt_domain_get_name($dom);
    return ($tmp) ? $tmp : $this->_set_last_error();
}


    /**
     * @param $domain
     * @return false
     * La fonction est utilisée pour suspendre le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function suspendre($domain) {
    $dom = $this->objet_domaine($domain);
    if (!$dom)
        return false;

    $tmp = libvirt_domain_suspend($dom);
    return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $domain
     * @return false
     * La fonction est utilisée pour éteindre le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function eteint($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_destroy($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $domain
     * @return false
     * La fonction est utilisée pour reprendre le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function reprendre($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_resume($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }


    /**
     * @param $dom
     * @return bool
     * La fonction est utilisée pour mettre en pause le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function pause($dom){
        $dom = $this->objet_domaine($dom);
        if(!$dom)
            return false;
        $tmp = $this->domain_get_info($dom);
        if(!$tmp)
            return $this->_set_last_error();
        $ret = ( ($tmp['state'] == VIR_DOMAIN_PAUSED));
        unset($tmp);
        return $ret;
    }


    /**
     * @param $dom
     * @return mixed
     * La fonction est utilisée pour démarrer le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function demarrer($dom) {
        $dom=$this->objet_domaine($dom);
        if ($dom) {
            $ret = libvirt_domain_create($dom);
            $this->last_error = libvirt_get_last_error();
            return $ret;
        }

        $ret = libvirt_domain_create_xml($this->conn, $dom);
        $this->last_error = libvirt_get_last_error();
        return $ret;
    }


    /**
     * @param $domain
     * @return false
     * La fonction est utilisée pour supprimer le domaine identifié par sa ressource.
     * TRUE en cas de succès, FALSE en cas d'erreur
     */
    function domain_undefine($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_undefine($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }


    /**
     * @return array
     * La fonction est utilisée pour obtenir les domaines.
     */
    function get_domains() {

        $response = array();
        $i = 0;

        $domains = libvirt_list_domain_resources($this->conn);
        foreach ($domains as $dom)
        {
            $response[$i]["name"] = libvirt_domain_get_name($dom);

            $response[$i]["uuid"] = libvirt_domain_get_uuid_string($dom);

    
            $dominfo=libvirt_domain_get_info($dom);
            

            $response[$i]["etat"] = $this->etat($dominfo['state']);


            $i++;

        }

       return $response;
    }

    /**
     * @return array
     * La fonction est utilisée pour obtenir les informations de tout les domaines.
     */
    public  function getinfosToutMachine()
    {

            $response = array();
                $i = 0;
            $domains = libvirt_list_domain_resources($this->conn);

            foreach ($domains as $dom)
            {
                $response[$i]["name"] = libvirt_domain_get_name($dom);
    
                $response[$i]["uuid"] = libvirt_domain_get_uuid_string($dom);
    
        
                $dominfo=libvirt_domain_get_info($dom);


                $response[$i]["maxMemorie"] = $dominfo['maxMem'];
                
    
                $response[$i]["Memorie"] = $dominfo['memory'];


                $response[$i]["virtcpu"] = $dominfo['nrVirtCpu'];
                
                $response[$i]["cpused"] = $dominfo['cpuUsed'];
    
                $i++;
    
            }
    

            return $response;

    }


    /**
     * @return array
     * La fonction est utilisée pour obtenir les informations d'un domaine à partir de son nom.
     */
    public function get_one_domain($domaineName)
    {

        $response = array();

        $dom = $this->objet_domaine($domaineName);
        $response["name"] = libvirt_domain_get_name($dom);

        $response["uuid"] = libvirt_domain_get_uuid_string($dom);
        $dominfo=libvirt_domain_get_info($dom);
            

        $response["etat"] = $this->etat($dominfo['state']);



        return $response;

        
    }

    /**
     * @param $domaineName
     * @return mixed
     * La fonction est utilisée pour obtenir l'UUID du domaine au format chaîne.
     */
    public function get_domaineUUID_By_name($domaineName)
    {
        $uuid = "";
        $dom = $this->objet_domaine($domaineName);
        $nom = libvirt_domain_get_uuid_string($dom);

       return $uuid = $nom;

    }

    /**
     * @param $nom_domain
     * @return array
     * La fonction est utilisée pour obtenir l'état du domaine.
     */
    public function get_domain_etat($nom_domain)
    {
        $response = array();

        $dom = $this->objet_domaine($nom_domain);
        $dominfo=libvirt_domain_get_info($dom);

        $response["etat"] = $this->etat($dominfo['state']);

        return $response;

    }

    /**
     * @param $xml
     * @return false
     * La fonction est utilisée pour définir le domaine à partir de la chaîne XML.
     */
    function domain_define($xml) {
        $tmp = libvirt_domain_define_xml($this->conn, $xml);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @return false
     *  La fonction est utilisée pour répertorier les pools de stockage
     */
    function get_storagepools() {
        $tmp = libvirt_list_storagepools($this->conn);
        if ($tmp)
            sort($tmp, SORT_NATURAL);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $res
     * @return false|resource
     * La fonction est utilisée pour rechercher le ressource de pool de stockage libvirt.
     *
     */
    function get_storagepool_res($res) {
        if ($res == false)
            return false;
        if (is_resource($res))
            return $res;

        $tmp = libvirt_storagepool_lookup_by_name($this->conn, $res);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $name
     * @return false
     * La fonction est utilisée pour rechercher le pool de stockage par son nom.
     */
    function get_storagepool_by_name($name){
        $tmp = libvirt_storagepool_lookup_by_name($this->conn, $name);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $pool
     * @param false $name
     * @return array|false
     * La fonction est utilisée pour obtenir les informations de pool de stockage
     */
    function storagepool_get_volume_information($pool, $name=false) {
        if (!is_resource($pool))
            $pool = $this->get_storagepool_res($pool);
        if (!$pool)
            return false;

        $out = array();
        $tmp = libvirt_storagepool_list_volumes($pool);
        for ($i = 0; $i < sizeof($tmp); $i++) {
            if (($tmp[$i] == $name) || ($name == false)) {
                $r = libvirt_storagevolume_lookup_by_name($pool, $tmp[$i]);
                $out[$tmp[$i]] = libvirt_storagevolume_get_info($r);
                $out[$tmp[$i]]['path'] = libvirt_storagevolume_get_path($r);
                unset($r);
            }
        }

        return $out;
    }

    /**
     * @param $pool
     * @return bool
     * La fonction est utilisée pour actualiser les informations du pool de stockage.
     */
    function recharger_vol($pool){
        if(!is_resource($pool))
            $pool = $this->get_storagepool_res($pool);    
        if(!$pool)
            return false;
        
        if(!libvirt_storagepool_refresh($pool, 0))
            return $this->_set_last_error();
        
        return true;
    }

    /**
     * @param $path
     * @return bool
     * La fonction est utilisée pour supprimer un pool de stockage
     */
    function supprimer_vol($path) {
        $vol = libvirt_storagevolume_lookup_by_path($this->conn, $path);
        if (!libvirt_storagevolume_delete($vol))
            return $this->_set_last_error();

        return true;
    }

    /**
     * @return false
     * La fonction est utilisée pour obtenir les informations sur le nœud hôte, principalement la mémoire totale installée,
     * le nombre total de processeurs installés et les informations sur le modèle sont utiles.
     */
    function host_node_info() {
        $tmp = libvirt_node_get_info($this->conn);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @return false
     * La fonction est utilisée pour obtenir les informations sur la connexion.
     *  renvoie: tableau d'informations sur la connexion
     */
    function connexion_information() {
        $tmp = libvirt_connect_get_information($this->conn);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     *
     * La fonction est utilisée pour obtenir les informations sur le nœud hôte, principalement la mémoire totale installée,
     * le nombre total de processeurs installés et les informations sur le modèle sont utiles.
     */
    public function gethostinfo()
    {
        global $lib;

        $response = array();
        $i=0;

        $tmp = $lib->host_node_info();
        $ci  = $lib->connexion_information();

        $info='';
        if ($ci['uri'])
        $info .= 'connecté avec '.$ci['uri'].' on '.$ci['hostname'].' ';
        if ($ci['encrypted'] == 'Yes')
        $info .= 'encrypted, ';
        if ($ci['secure'] == 'Yes')
        $info .= 'secure, ';
        if ($ci['hypervisor_maxvcpus'])
        $info .= 'maximum '.$ci['hypervisor_maxvcpus'].' vcpus per guest, ';


        $response[$i]['hyp'] = $ci['hypervisor_string'];
        $response[$i]['infoC'] = $info;
        $response[$i]['arc']= $tmp['model'];
        $response[$i]['Minstalle'] = number_format(($tmp['memory'] / 1048576), 2, '.', ' ')."GB";
        $response[$i]['np'] = $tmp['cpus'];
        $response[$i]['vp'] = $tmp['mhz']."MHz";
        $response[$i]['nodp'] = $tmp['nodes'];
        $response[$i]['pp'] = $tmp['sockets'];
        $response[$i]['cp'] = $tmp['cores'];
        $response[$i]['tp'] = $tmp['threads'];

        return $response;

    }

    /**
     * @param $type
     * @return false
     * La fonction est utilisée pour obtenir tout les réseaux
     */
    function get_reseaux($type = VIR_NETWORKS_ALL) {
        $tmp = libvirt_list_networks($this->conn, $type);
        if ($tmp)
            sort($tmp, SORT_NATURAL);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $network
     * @return false|resource
     * La fonction est utilisée pour obtenir la ressource réseau à partir du nom.
     */
    function get_reseau_res($network) {
        if ($network == false)
            return false;
        if (is_resource($network))
            return $network;

        $tmp = libvirt_network_get($this->conn, $network);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $network
     * @return false
     * La fonction est utilisée pour obtenir l'état d'activité du réseau.
     */
    function get_reseau_active($network) {
        $res = $this->get_reseau_res($network);
        if ($res == false)
            return false;

        $tmp = libvirt_network_get_active($res);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $network
     * @return false
     * La fonction est utilisée pour obtenir les informations sur plage de réseau, IP de réseau, type de réseau et etc.
     */
    function get_information_reseau($network) {
        $res = $this->get_reseau_res($network);
        if ($res == false)
            return false;

        $tmp = libvirt_network_get_information($res);
        if (!$tmp)
            return $this->_set_last_error();
        $tmp['active'] = $this->get_reseau_active($res);
        return $tmp;
    }
    /**
     * @param $network
     * @return false
     * La fonction est utilisée pour obtenir les informations sur plage de réseau, IP de réseau, type de réseau et etc.
     */
    public function infoReseau()
    {
        global $lib;
        $response = array();
    

        $tmp = $lib->get_reseaux(VIR_NETWORKS_ALL);

        for ($i = 0; $i < sizeof($tmp); $i++) {
            $tmp2 = $lib->get_information_reseau($tmp[$i]);
            $ip = '';
            $ip_range = '';
            $activity = $tmp2['active'] ? 'Active' : 'Inactive';
            $dhcp = 'Disabled';
            $forward = 'None';
            if (array_key_exists('forwarding', $tmp2) && $tmp2['forwarding'] != 'None') {
                if (array_key_exists('forward_dev', $tmp2))
                    $forward = $tmp2['forwarding'].' to '.$tmp2['forward_dev'];
                else
                    $forward = $tmp2['forwarding'];
            }

            if (array_key_exists('dhcp_start', $tmp2) && array_key_exists('dhcp_end', $tmp2))
                $dhcp = $tmp2['dhcp_start'].' - '.$tmp2['dhcp_end'];

            if (array_key_exists('ip', $tmp2))
                $ip = $tmp2['ip'];

            if (array_key_exists('ip_range', $tmp2))
                $ip_range = $tmp2['ip_range'];

        
        }

        $response[$i]['ip'] = $ip;
        $response[$i]['ipRange'] = $ip_range;
        $response[$i]['etat'] = $activity;
        $response[$i]['forwarding'] = $forward;
        $response[$i]['dhcp'] = $dhcp;
        $response[$i]['nomr'] = $tmp2['name'];
       
        return $response;
        
    }

    /**
     * @param $network
     * @return false|resource
     * La fonction est utilisée pour obtenir la ressource de réseau
     */
    function res_reseau($network) {
        if ($network == false)
            return false;
        if (is_resource($network))
            return $network;

        $tmp = libvirt_network_get($this->conn, $network);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    /**
     * @param $network
     * @param bool $active
     * @return bool
     * La fonction est utilisée pour activer le réseau
     */
    function activer_reseau($network, $active = true) {
        $res = $this->res_reseau($network);
        if ($res == false)
            return false;
        if (!libvirt_network_set_active($res, $active ? 1 : 0))
            return $this->_set_last_error();
        return true;
    }

    /**
     * @param $network
     * @param $xml
     * @return bool
     * La fonction est utilisée pour modifier le fichier xml de réseau enfin de distribuer des IP fixes aux machines.
     */
    function network_change_xml($network, $xml) {
        $net = $this->res_reseau($network);

        if (!($old_xml = libvirt_network_get_xml_desc($net, NULL))) {
            return $this->_set_last_error();
        }
        if (!libvirt_network_undefine($net)) {
            return $this->_set_last_error();
        }
        if (!libvirt_network_define_xml($this->conn, $xml)) {
            $this->last_error = libvirt_get_last_error();
            libvirt_network_define_xml($this->conn, $old_xml);
            return false;
        }

        return true;
    }

}

?>