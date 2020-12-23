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
                <type arch="x86_64" machine="pc-i440fx-focal">hvm</type>	
                <boot dev="hd"/>	
              </os>	
              <features>	
                <acpi/>	
                <apic/>	
                <vmport state="off"/>	
              </features>	
              <cpu mode="host-model" check="partial"/>	
              <clock offset="utc">	
                <timer name="rtc" tickpolicy="catchup"/>	
                <timer name="pit" tickpolicy="delay"/>	
                <timer name="hpet" present="no"/>	
              </clock>	
              <on_poweroff>destroy</on_poweroff>	
              <on_reboot>restart</on_reboot>	
              <on_crash>destroy</on_crash>	
              <pm>	
                <suspend-to-mem enabled="no"/>	
                <suspend-to-disk enabled="no"/>	
              </pm>	
              <devices>	
                <emulator>/usr/bin/qemu-system-x86_64</emulator>	
                <disk type="file" device="disk">	
                  <driver name="qemu" type="qcow2"/>	
                  <source file="' . $newfile . '"/>	
                  <target dev="hda" bus="ide"/>	
                  <address type="drive" controller="0" bus="0" target="0" unit="0"/>	
                </disk>	
                <disk type="file" device="cdrom">	
                  <driver name="qemu" type="raw"/>	
                  <target dev="hdb" bus="ide"/>	
                  <readonly/>	
                  <address type="drive" controller="0" bus="0" target="0" unit="1"/>	
                </disk>	
                <controller type="usb" index="0" model="ich9-ehci1">	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x05" function="0x7"/>	
                </controller>	
                <controller type="usb" index="0" model="ich9-uhci1">	
                  <master startport="0"/>	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x05" function="0x0" multifunction="on"/>	
                </controller>	
                <controller type="usb" index="0" model="ich9-uhci2">	
                  <master startport="2"/>	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x05" function="0x1"/>	
                </controller>	
                <controller type="usb" index="0" model="ich9-uhci3">	
                  <master startport="4"/>	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x05" function="0x2"/>	
                </controller>	
                <controller type="pci" index="0" model="pci-root"/>	
                <controller type="ide" index="0">	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x01" function="0x1"/>	
                </controller>	
                <controller type="virtio-serial" index="0">	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x06" function="0x0"/>	
                </controller>	
                <interface type="network">	
                  <mac address="' . $generated_mac . '"/>
                  <source network="default"/>	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x03" function="0x0"/>	
                </interface>	
                <serial type="pty">	
                  <target type="isa-serial" port="0">	
                    <model name="isa-serial"/>	
                  </target>	
                </serial>	
                <console type="pty">	
                  <target type="serial" port="0"/>	
                </console>	
                <channel type="spicevmc">	
                  <target type="virtio" name="com.redhat.spice.0"/>	
                  <address type="virtio-serial" controller="0" bus="0" port="1"/>	
                </channel>	
                <input type="tablet" bus="usb">	
                  <address type="usb" bus="0" port="1"/>	
                </input>	
                <input type="mouse" bus="ps2"/>	
                <input type="keyboard" bus="ps2"/>	
                <graphics type="spice" autoport="yes">	
                  <listen type="address"/>	
                  <image compression="off"/>	
                </graphics>	
                <sound model="ich6">	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x04" function="0x0"/>	
                </sound>	
                <video>	
                  <model type="qxl" ram="65536" vram="65536" vgamem="16384" heads="1" primary="yes"/>	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x02" function="0x0"/>	
                </video>	
                <redirdev bus="usb" type="spicevmc">	
                  <address type="usb" bus="0" port="2"/>	
                </redirdev>	
                <redirdev bus="usb" type="spicevmc">	
                  <address type="usb" bus="0" port="3"/>	
                </redirdev>	
                <memballoon model="virtio">	
                  <address type="pci" domain="0x0000" bus="0x00" slot="0x07" function="0x0"/>	
                </memballoon>	
              </devices>	
            </domain>';
if (!$lib->domain_define($vmxml)) {
    die('Error lors de création de template');
} else {
    echo('la machine ' . $vmname . ' a bien été créé !');
}

$reponse = "le template à bien été créé";
 }else
 {
   $reponse = "Erreur est survenue";
 }



  


     header('Content-Type: application/json');
     echo json_encode($reponse, JSON_PRETTY_PRINT);




  }