
<?php

session_start();

?>

<html>
	<head>
		<title></title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href="../assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />



        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
        <!-- CSS Files -->
        <link href="../assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />
        <!-- CSS Just for demo purpose, don't include it in your project -->
        <link href="../assets/demo/demo.css" rel="stylesheet" />
        <style>
            #dynamic_field_modal{padding-top: 10%}
        </style>



	</head>


	<body>

    <div class="wrapper ">
        <div class="sidebar" data-color="azure" data-background-color="black" data-image="../assets/img/bg2.jpg">

            <div class="logo"><a href="index.php" class="simple-text logo-normal">
                    G-VMs
                </a></div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="nav-item ">
                        <a class="nav-link" href="../../divers/hote.php">
                            <i class="material-icons">portrait</i>
                            <p>Info-Hôte</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/infoVm.php">
                            <i class="material-icons">info</i>
                            <p>Info-VMs</p>
                        </a>
                    </li>

                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/reseau.php">
                            <i class="material-icons">language</i>
                            <p>Réseau</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/pool.php">
                            <i class="material-icons">dns</i>
                            <p>Pools</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/delpool.php">
                            <i class="material-icons">delete</i>
                            <p>Supprimer Pools</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/creerVm.php">
                            <i class="material-icons">add_to_queue</i>
                            <p>Création de VMs</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link" href="../divers/creerTemplate.php">
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
                </ul>
            </div>


        </div>
        <div class="main-panel">
    <div class="container">
             <br />
             <br />
             <h2 align="center text-info">Création de Groupes</h2><br />
             <div align="right">
                 <button type="button" name="add" id="add" class="btn btn-success">Créer un groupe</button>
             </div>
             <br />

                <div id="result"> </div>
         </div>
        </div>
    </div>

	</body>
</html>

<div id="dynamic_field_modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" id="add_name">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Créer</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
		      			<input type="text" name="groupe" id="name" class="form-control" placeholder="Nom de groupe" />
		      		</div>
		      		<div class="table-responsive">
		      			<table class="table " id="dynamic_field">

		      			</table>
		      		</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="hidden_id" id="hidden_id" />
					<input type="hidden" name="action" id="action" value="insert" />
					<input type="submit" name="submit" id="submit" class="btn btn-info" value="Créer" />
				</div>
			</form>
		</div>
	</div>

</div>

</div>
<script>
$(document).ready(function(){

	load_data();

	var count = 1;

	function load_data()
	{
		$.ajax({
			url:"fetch.php",
			method:"POST",
			success:function(data)
			{
				$('#result').html(data);
			}
		})
	}

	function add_dynamic_input_field(count)
	{
		var button = '';
		if(count > 1)
		{
			button = '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">x</button>';
		}
		else
		{
			button = '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
		}
		output = '<tr id="row'+count+'">';
		output += '<td><input type="text" name="vms[]" placeholder="Nom de la machine" class="form-control name_list" /></td>';
		output += '<td align="center">'+button+'</td></tr>';
		$('#dynamic_field').append(output);
	}

	$('#add').click(function(){
		$('#dynamic_field').html('');
		add_dynamic_input_field(1);
		$('.modal-title').text('Créer un groupe');
		$('#action').val("insert");
		$('#submit').val('Créer');
		$('#add_name')[0].reset();
		$('#dynamic_field_modal').modal('show');
	});

	$(document).on('click', '#add_more', function(){
		count = count + 1;
		add_dynamic_input_field(count);
	});

	$(document).on('click', '.remove', function(){
		var row_id = $(this).attr("id");
		$('#row'+row_id).remove();
	});

	$('#add_name').on('submit', function(event){
		event.preventDefault();
		if($('#name').val() == '')
		{
			alert("nom de la groupe");
			return false;
		}
		var total_languages = 0;
		$('.name_list').each(function(){
			if($(this).val() != '')
			{
				total_languages = total_languages + 1;
			}
		});

		if(total_languages > 0)
		{
			var form_data = $(this).serialize();

			var action = $('#action').val();
			$.ajax({
				url:"action.php",
				method:"POST",
				data:form_data,
				success:function(data)
				{
					if(action == 'insert')
					{
						alert("Données ajoutées");
					}
					if(action == 'edit')
					{
						alert("Données modifiées");
					}
					add_dynamic_input_field(1);
					load_data();
					$('#add_name')[0].reset();
					$('#dynamic_field_modal').modal('hide');
				}
			});
		}
		else
		{
			alert("Veuillez saisir au moins un nom de machine");
		}
	});

	$(document).on('click', '.edit', function(){
		var id = $(this).attr("id");
		$.ajax({
			url:"select.php",
			method:"POST",
			data:{id:id},
			dataType:"JSON",
			success:function(data)
			{
				$('#name').val(data.groupe);
				$('#dynamic_field').html(data.vms);
				$('#action').val('edit');
				$('.modal-title').text("Modification");
				$('#submit').val("Edit");
				$('#hidden_id').val(id);
				$('#dynamic_field_modal').modal('show');
			}
		});
	});

	$(document).on('click', '.delete', function(){
		var id = $(this).attr("id");
		if(confirm("Voulez-vous vraiment supprimer ces données?"))
		{
			$.ajax({
				url:"delete.php",
				method:"POST",
				data:{id:id},
				success:function(data)
				{
					load_data();
					alert("Données supprimées");
				}
			})
		}
	});

});
</script>
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




