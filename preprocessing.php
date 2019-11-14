<!DOCTYPE html>
<html>
<?php
    include("koneksi.php");
    include("fungsi.php");
?>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Preprocessing</title>

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/datepicker3.css" rel="stylesheet">
		<link href="css/bootstrap-table.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">

		<!--Theme Switcher-->
		<style id="hide-theme">
			body{
				display:none;
			}
		</style>
		<script type="text/javascript">
			function setTheme(name){
				var theme = document.getElementById('theme-css');
				var style = 'css/theme-' + name + '.css';
				if(theme){
					theme.setAttribute('href', style);
				} else {
					var head = document.getElementsByTagName('head')[0];
					theme = document.createElement("link");
					theme.setAttribute('rel', 'stylesheet');
					theme.setAttribute('href', style);
					theme.setAttribute('id', 'theme-css');
					head.appendChild(theme);
				}
				window.localStorage.setItem('lumino-theme', name);
			}
			var selectedTheme = window.localStorage.getItem('lumino-theme');
			if(selectedTheme) {
				setTheme(selectedTheme);
			}
			window.setTimeout(function(){
					var el = document.getElementById('hide-theme');
					el.parentNode.removeChild(el);
				}, 5);
		</script>
		<!-- End Theme Switcher -->


		<!--Custom Font-->
		<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse"><span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span></button>
					<a class="navbar-brand" href="#"><span>Query Answering</span> System</a>
				</div>
			</div><!-- /.container-fluid -->
		</nav>
		<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
			<div>
				<img src="images/qas_hadith_icon.png" width="180" class="img-responsive" alt="">
			</div>
			<div class="divider"></div>
			<ul class="nav menu">
				<li><a class="" href="index.php"><em class="fa fa-home">&nbsp;</em> Home</a></li>
				<li><a class="" href="search.php"><span class="fa fa-desktop">&nbsp;</span> Query Answering System</a></li>
				<li class="active"><a class="" href="preprocessing.php"><span class="fa fa-recycle">&nbsp;</span> Preprocessing</a></li>
				<li class="parent "><a data-toggle="collapse" href="#sub-item-1">
					<em class="fa fa-file-o">&nbsp;</em> Data Set <span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><i class="fa fa-plus"></i></span>
					</a>
					<ul class="children collapse" id="sub-item-1">
						<li class="active"><a class="" href="dataset.php"><span class="fa fa-book">&nbsp;</span>
							Hadith Data
						</a></li>
						<li><a class="" href="data_thesaurus.php"><span class="fa fa-book">&nbsp;</span>
							Thesaurus Data
						</a></li>
						<li><a class="" href="data_sim_thesaurus.php"><span class="fa fa-book">&nbsp;</span>
							Similarity Thesaurus Data
						</a></li>
					</ul>
				</li>
			</ul>
		</div><!--/.sidebar-->
			
		<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
			<div class="row">
				<ol class="breadcrumb">
					<li><a href="#">
						<em class="fa fa-recycle"></em>
					</a></li>
					<li class="active">Preprocessing</li>
				</ol>
			</div><!--/.row-->
			
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-info">
						<div class="panel panel-heading">Preprocessing</div>
					</div>
				</div>
    		</div><!--/.row-->
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body btn-margins">
                            <form method="POST" action="fungsi.php">
                                <p>Click the button below to start preprocessing.</p>
                                <div>
                                    <button type="button" class="btn btn-lg btn-warning fa fa-recycle" onclick="preproses()">Preprocessing</button>
                                </div>
                            </form>
                            <p><code id="notif"></code></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lihat Indeks -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel panel-heading">Index Data of Hadith Term</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
						<div class="panel-body">
							<table data-toggle="table"  data-show-refresh="true" data-show-toggle="true" data-show-columns="true" data-search="true" data-select-item-name="toolbar1" data-pagination="true" data-sort-name="name" data-sort-order="desc">
								<thead>
								<tr>
									<th data-field="id" data-sortable="true">Number</th>
									<th data-field="nomor_hadis"  data-sortable="true">Hadith Number</th>
									<th data-field="term" data-sortable="true">Term</th>
									<th data-field="count" data-sortable="true">Amount</th>
								</tr>
								</thead>
								<tbody>
								<?php
								mysqli_query($connect,"SET CHARACTER SET utf8");
								$query = mysqli_query($connect, "SELECT * FROM tb_index ORDER by id ASC") or die(("Can't Connect Database"));
								$no = 1;
								while ($row = mysqli_fetch_assoc($query)) {
									?>
										<tr>
											<td><?php echo $no;?></td>
											<td><?php echo $row['nomor_hadis'];?></td>
											<td><?php echo $row['term'];?></td>
											<td><?php echo $row['count'];?></td>
										</tr>
									<?PHP
									$no++;
								}
								?>
								</tbody>
							</table>
						</div>
                    </div>
                </div>
            </div>
		</div>	<!--/.main-->
		
		<script src="js/jquery-1.11.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/chart.min.js"></script>
		<script src="js/chart-data.js"></script>
		<script src="js/easypiechart.js"></script>
		<script src="js/easypiechart-data.js"></script>
        <script src="js/bootstrap-datepicker.js"></script>
        <script src="js/bootstrap-table.js"></script>
		<script src="js/custom.js"></script>
        
        <script type="text/javascript">
        function preproses(){
            var preprosesx = "preproses";
            $.ajax({
                type : "POST",
                url : "fungsi.php",
                data: {preproses:preprosesx},
                error: function(){
                    $("#notif").prepend("gagal");
                },
                success: function(html){
                    $("#notif").prepend("Preprosesing berhasil dilakukan<br/>"+html);
                    alert('Prepocessing Sukses');
                },
            });
            return false;
        }
    	</script>
	</body>
</html>