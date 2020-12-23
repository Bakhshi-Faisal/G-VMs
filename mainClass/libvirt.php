<?php

class Libvirt {
    private $conn;
    private $last_error;
    private $allow_cached = true;
    private $dominfos = array();


    

    function __construct($debug = false) {
        if ($debug)
            $this->set_logfile($debug);
    }

    function _set_last_error() {
        $this->last_error = libvirt_get_last_error();
        return false;
    }

    function set_logfile($filename) {
        if (!libvirt_logfile_set($filename))
            return $this->_set_last_error();

        return true;
    }



    function connexion($uri = 'null') {
        $this->conn=libvirt_connect($uri, false);
        if ($this->conn==false)
            return $this->_set_last_error();
        return true;
    }

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




   function get_last_error() {
    return $this->last_error;
}


   function domain_get_name_by_uuid($uuid) {
    $dom = libvirt_domain_lookup_by_uuid_string($this->conn, $uuid);
    if (!$dom)
        return false;
    $tmp = libvirt_domain_get_name($dom);
    return ($tmp) ? $tmp : $this->_set_last_error();
}





   function suspendre($domain) {
    $dom = $this->objet_domaine($domain);
    if (!$dom)
        return false;

    $tmp = libvirt_domain_suspend($dom);
    return ($tmp) ? $tmp : $this->_set_last_error();
    }

    function eteint($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_destroy($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    
    function reprendre($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_resume($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }


    function eteindre($domain, $name=false){
        $dom = $this->objet_domaine($domain);
        if(!$dom)
            return false;
        $tmp = $this->domain_get_info($dom);
        if(!$tmp)
            return $this->_set_last_error();
        $ret = ( ($tmp['state'] == VIR_DOMAIN_SHUTDOWN) || ($tmp['state'] == VIR_DOMAIN_SHUTOFF) );
        unset($tmp);
        return $ret;
    }


    function pause($dom){
        $dom = $this->objet_domaine($domain);
        if(!$dom)
            return false;
        $tmp = $this->domain_get_info($dom);
        if(!$tmp)
            return $this->_set_last_error();
        $ret = ( ($tmp['state'] == VIR_DOMAIN_PAUSED));
        unset($tmp);
        return $ret;
    }



    function Encours($dom) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;
        $tmp = $this->domain_get_info($dom);
        if (!$tmp)
            return $this->_set_last_error();
        $ret = ( ($tmp['state'] == VIR_DOMAIN_RUNNING) || ($tmp['state'] == VIR_DOMAIN_BLOCKED) );
        unset($tmp);
        return $ret;
    }

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


    function domain_undefine($domain) {
        $dom = $this->objet_domaine($domain);
        if (!$dom)
            return false;

        $tmp = libvirt_domain_undefine($dom);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    


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

    public function get_domaineUUID_By_name($domaineName)
    {
        $uuid = "";
        $dom = $this->objet_domaine($domaineName);
        $nom = libvirt_domain_get_uuid_string($dom);

       return $uuid = $nom;

    }

    public function get_domain_etat($nom_domain)
    {
        $response = array();

        $dom = $this->objet_domaine($nom_domain);
        $dominfo=libvirt_domain_get_info($dom);

        $response["etat"] = $this->etat($dominfo['state']);

        return $response;

    }


    function domain_define($xml) {
        $tmp = libvirt_domain_define_xml($this->conn, $xml);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    function get_storagepools() {
        $tmp = libvirt_list_storagepools($this->conn);
        if ($tmp)
            sort($tmp, SORT_NATURAL);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    function get_storagepool_res($res) {
        if ($res == false)
            return false;
        if (is_resource($res))
            return $res;

        $tmp = libvirt_storagepool_lookup_by_name($this->conn, $res);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    
    function get_storagepool_by_name($name){
        $tmp = libvirt_storagepool_lookup_by_name($this->conn, $name);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

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
    public function get_all_volumes()
    {


        $t=libvirt_list_storagepools($conn); 
        $pres=libvirt_storagepool_lookup_by_name($conn, $t);
         

    }
    function recharger_vol($pool){
        if(!is_resource($pool))
            $pool = $this->get_storagepool_res($pool);    
        if(!$pool)
            return false;
        
        if(!libvirt_storagepool_refresh($pool, 0))
            return $this->_set_last_error();
        
        return true;
    }
    
    function supprimer_vol($path) {
        $vol = libvirt_storagevolume_lookup_by_path($this->conn, $path);
        if (!libvirt_storagevolume_delete($vol))
            return $this->_set_last_error();

        return true;
    }

    function host_node_info() {
        $tmp = libvirt_node_get_info($this->conn);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

    function connexion_information() {
        $tmp = libvirt_connect_get_information($this->conn);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }


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

    function get_reseaux($type = VIR_NETWORKS_ALL) {
        $tmp = libvirt_list_networks($this->conn, $type);
        if ($tmp)
            sort($tmp, SORT_NATURAL);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    function get_reseau_res($network) {
        if ($network == false)
            return false;
        if (is_resource($network))
            return $network;

        $tmp = libvirt_network_get($this->conn, $network);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    function get_reseau_active($network) {
        $res = $this->get_reseau_res($network);
        if ($res == false)
            return false;

        $tmp = libvirt_network_get_active($res);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }

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

    function res_reseau($network) {
        if ($network == false)
            return false;
        if (is_resource($network))
            return $network;

        $tmp = libvirt_network_get($this->conn, $network);
        return ($tmp) ? $tmp : $this->_set_last_error();
    }
    function activer_reseau($network, $active = true) {
        $res = $this->res_reseau($network);
        if ($res == false)
            return false;
        if (!libvirt_network_set_active($res, $active ? 1 : 0))
            return $this->_set_last_error();
        return true;
    }







  


 

 
}

?>