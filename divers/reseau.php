<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>
        Material Dashboard by Creative Tim
    </title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- CSS Files -->
    <link href="../assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="../assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="">
<div class="wrapper ">
    <div class="sidebar" data-color="azure" data-background-color="black" data-image="../assets/img/bg2.jpg">

        <div class="logo"><a href="../index.php" class="simple-text logo-normal">
                G-VMs
            </a></div>
        <div class="sidebar-wrapper">
            <ul class="nav">
                <li class="nav-item ">
                    <a class="nav-link" href="hote.php">
                        <i class="material-icons">portrait</i>
                        <p>Info-Hôte</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="infoVm.php">
                        <i class="material-icons">info</i>
                        <p>Info-VMs</p>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="reseau.php">
                        <i class="material-icons">language</i>
                        <p>Réseau</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="pool.php">
                        <i class="material-icons">dns</i>
                        <p>Pools</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="delpool.php">
                        <i class="material-icons">delete</i>
                        <p>Supprimer Pools</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="creerVm.php">
                        <i class="material-icons">add_to_queue</i>
                        <p>Création de VMs</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="creerTemplate.php">
                        <i class="material-icons">add_to_queue</i>
                        <p>Création de template</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="../groupe/index.php">
                        <i class="material-icons">group_work</i>
                        <p>Groupe</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="gestionGroup.php">
                        <i class="material-icons">group_work</i>
                        <p>Gestion de Groupes</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="changeip.php">
                        <i class="material-icons">group_work</i>
                        <p>Gestion de IPs fixes</p>
                    </a>
                </li>
            </ul>
        </div>


    </div>
    <div class="main-panel">

        <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="javascript:;">Gestion de Réseau</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end">



                    <?php



                    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){

                        echo '
                <a class="nav-link" href="../login/deconnexion.php">Déconnexion</a>
                   ';

                    }
                    else
                    {

                        echo '
                    <a class="nav-link" href="../login/login.php">Connexion</a>
                      ';


                    }


                    ?>

                </div>
            </div>
        </nav>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header card-header-info">
                                <h4 class="card-title ">Réseau</h4>
                            </div>
                            <div class="card-body">


                                <?php

                                ini_set('display_startup_errors',1);
                                ini_set('display_errors',1);
                                error_reporting(-1);

                                $response = httpGet('http://127.0.0.1/api/infoReseau.php');
                                $response = json_decode($response, true);


                                echo '<table class="table table-responsive">
                                        <thead class="text-info text-center">
                                        <th>
                                            Nom de réseau
                                        </th>
                                        <th>
                                            État du réseau
                                        </th>
                                        <th>
                                            Adresse IP de la passerelle
                                        </th>
                                        <th>
                                            Plage d\'adresses IP
                                        </th>
                                        <th>
                                            Type de réseau
                                        </th>
                                        <th>
                                           Plage DHCP
                                        </th> 
                                        <th>
                                           Action
                                        </th>
                                        </thead>
                                        <tbody class="text-center">';
                                foreach ($response as $m) {

                                    echo
                                        "<tr>" .
                                        "<td class='text-center'>" . $m['nomr'] . "</td>" .
                                        "<td class='text-center'>" . $m['etat'] . "</td>" .
                                        "<td class='text-center'>" . $m['ip'] . "</td>".
                                        "<td class='text-center'>" . $m['ipRange'] . "</td>".
                                        "<td class='text-center'>" . $m['forwarding'] . "</td>".
                                        "<td class='text-center'>" . $m['dhcp'] . "</td>";

                                    echo '<td class=" text-center">';

                                    if($m['etat'] == "Active")
                                    {
                                        echo '<a href=?action=arreter-reseau&nomr='. $m['nomr'] .'> Arreter le réseau </a>' ;
                                    }
                                    if($m['etat'] == "Inactive")
                                    {
                                        echo '<a href=?action=demarrer-reseau&nomr='. $m['nomr'] .'> Démarrer le réseau </a>' ;
                                        echo '<a href=?action=-reseau&nomr='. $m['nomr'] .'> Démarrer le réseau </a>' ;
                                    }

                                    echo "</td>";


                                }
                                echo'</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>';

                                $action = array_key_exists('action', $_GET) ? $_GET['action'] : false;

                                if ($action) {
                                    $url = "http://localhost/api/reseauEtat.php?nomr=" . $_GET['nomr'];
                                    $data = array('action' => $_GET['action']);


                                    $response = httpPut($url, $data);
                                    $response = json_decode($response, true);
                                    if(!empty($response)){
                                        if ($response["status"] === 1) {
                                            echo $response['status_message'];
                                        } else if ($response["status"] === 0) {
                                            echo "ohhh noo";
                                        }
                                    }
                                }

                                function httpGet($url)
                                {
                                    $ch = curl_init();

                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                    $output = curl_exec($ch);
                                    curl_close($ch);
                                    return $output;
                                }

                                function httpPut($url, $params)
                                {
                                    $ch = curl_init($url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                                    $output = curl_exec($ch);
                                    curl_close($ch);
                                    return $output;
                                }







                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="copyright float-right">
                    &copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script>, Développé par BAKHSHI Faisal

                </div>
            </div>
        </footer>
    </div>
</div>

<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap-material-design.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!-- Plugin for the momentJs  -->
<script src="../assets/js/plugins/moment.min.js"></script>
<!--  Plugin for Sweet Alert -->
<script src="../assets/js/plugins/sweetalert2.js"></script>
<!-- Forms Validations Plugin -->
<script src="../assets/js/plugins/jquery.validate.min.js"></script>
<!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<script src="../assets/js/plugins/jquery.bootstrap-wizard.js"></script>
<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
<!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
<script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="../assets/js/plugins/bootstrap-tagsinput.js"></script>
<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="../assets/js/plugins/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
<script src="../assets/js/plugins/fullcalendar.min.js"></script>
<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
<script src="../assets/js/plugins/jquery-jvectormap.js"></script>
<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="../assets/js/plugins/nouislider.min.js"></script>
<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<!-- Library for adding dinamically elements -->
<script src="../assets/js/plugins/arrive.min.js"></script>
<!--  Google Maps Plugin    -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Chartist JS -->
<script src="../assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="../assets/js/material-dashboard.js?v=2.1.2" type="text/javascript"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="../assets/demo/demo.js"></script>

<script>
    $(document).ready(function() {
        // Javascript method's body can be found in ../assets/js/demos.js
        md.initDashboardPageCharts();

    });
</script>
</body>

</html>
