<?php
// creation d'un template avec le fichier XML
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


   
        createTemplate();
           
        
      break;
    default:

  }

  function createTemplate()
  {
      
  global $lib;
  
 
  $nom = $_POST['vmname'];

  var_dump($nom);

 if(!empty($nom))
 {


    $nom = $_POST['vmname'];

    $disk = $_POST['disk'];


    $ram = $_POST['ram'];
    
    
    $cpucores = $_POST['cpucores'];

    $nom = "Template-".$nom;

    while(file_exists('/var/lib/libvirt/images/'.$nom.'.qcow2'))
    {

    $nom = "Template-".$nom;
    }
    


    var_dump($nom);

    
    $file = $disk;
    $newfile = '/var/lib/libvirt/images/'.$nom.'.qcow2';

   
     exec("cp '$file' '$newfile'", $output, $retrun_var);



     $generated_mac = exec('MACAddress="$(dd if=/dev/urandom bs=1024 count=1 2>/dev/null|md5sum|sed \'s/^\(..\)\(..\)\(..\)\(..\)\(..\)\(..\).*$/52:\2:\3:\4:\5:\6/\')";echo $MACAddress');
$vmxml = '<domain type="kvm">    
                  <name>' . $nom . '</name>    
                  <memory unit="GiB">' . $ram . '</memory>
                  <currentMemory unit="GiB">' . $ram . '</currentMemory>    
                  <vcpu placement="static">' . $cpucores . '</vcpu>    
                  <os>
                    <type arch="x86_64" machine="pc-i440fx-rhel7.0.0">hvm</type>
                    <boot dev="hd"/>
                  </os>
                  <on_poweroff>destroy</on_poweroff>    
                  <on_reboot>restart</on_reboot>    
                  <on_crash>destroy</on_crash>    
                  <devices>    
                    <emulator>/usr/libexec/qemu-kvm</emulator>    
                    <disk type="file" device="disk">    
                      <driver name="qemu" type="qcow2"/>    
                      <source file="' . $disk . '"/>    
                      <target dev="hda" bus="ide"/>    
                      <address type="drive" controller="0" bus="0" target="0" unit="0"/>    
                    </disk>
                    <interface type="network">    
                      <mac address="' . $generated_mac . '"/>
                      <source network="default"/>    
                      <address type="pci" domain="0x0000" bus="0x00" slot="0x03" function="0x0"/>    
                    </interface>    
                    <serial type="pty">
                        <target port="0"/>
                    </serial>
                    <console type="pty">
                        <target type="serial" port="0"/>
                    </console>
                  </devices>
                </domain>';
if (!$lib->domain_define($vmxml)) {
    die('Error lors de création de template');
} else {
    echo('la machine ' . $nom . ' a bien été créé !');
}

$reponse = "le template à bien été créé";
 }else
 {
   $reponse = "Erreur est survenue";
 }



  


     header('Content-Type: application/json');
     echo json_encode($reponse, JSON_PRETTY_PRINT);




  }