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
		<title>Search</title>

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/datepicker3.css" rel="stylesheet">
		<link href="css/bootstrap-table.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
		<style>
			input[type="checkbox"] { 
				width: 20px; 
				height: 20px;
				background: #34495E; 
        	} 
			input[type="checkbox"]:checked {
				background-color: #2ECC71;
			}
			label {
				padding:10px;
				margin:0 0 10px;
			}
		</style>
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
				<li class="active"><a class="" href="search.php"><span class="fa fa-desktop">&nbsp;</span> Query Answering System</a></li>
				<li><a class="" href="preprocessing.php"><span class="fa fa-recycle">&nbsp;</span> Preprocessing</a></li>
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
						<em class="fa fa-desktop"></em>
					</a></li>
					<li class="active">Query Answering System</li>
				</ol>
			</div><!--/.row-->
			<br>
			<div class="row">
				<div class="col-lg-8 col-lg-offset-4">
					<!-- <center> -->
                        <h2><b>Query Answering System</b></h2>
                    <!-- </center> -->
				</div>

				<div class="col-lg-12 ">
						<form method="get">
							<div class="input-group input-group-lg">
									<input type="text" class="form-control round-form" placeholder="Input Query" name="keyword" id="keyword">
									<div class="input-group-btn">
										<button type="submit" class="btn btn-round btn-info" name="cari" value="cari" onclick="cari()">Search</button>
									</div>
							</div>
							<br>
							<div class="row">
								<div class="col-lg-10 col-lg-offset-5">
									<div class="panel-body">
										<div class="col-lg-12">
											<div class="switch"
											data-on-label="<i class='fa fa-check'></i>"
											data-off-label="<i class='fa fa-times'></i>">
											<input class="largerCheckbox" type="checkbox" name="check1" value="qe" id="checkbox_id"/>
											<label for="checkbox_id"> Query Expansion</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<br>
						</form>
						<?php
						if(isset($_GET['cari'])){
							$keyword = $_GET['keyword'];
							if(($_GET['keyword'] <> "")){
								if(isset($_GET['check1'])){
									?>
									<div>
										<div class="showback">
											<?php
												//preproses
												$proses1= stopword($keyword);
												$proses2= stemming($proses1);
												$proses3= ekspansiTesaurus($proses2);
												echo ('<div class="alert alert-success"><b>Query Input : </b>'.$keyword.'</div>');
												echo ('<div class="alert alert-info"><b>Query Expansion : </b>'.$proses3.'</div>');
												
											?>
										</div>
										<div class="content-panel">
											<table class="table table-bordered table-striped table-condensed">
											<thead>
												<tr>
													<th><span class="badge bg-success"> Number</span></th>
													<th><span class="badge bg-success"> Book</span></th>
													<th><span class="badge bg-success"> Chapter</span></th>
													<th><span class="badge bg-success"> Hadith</span></th>
													<th><span class="badge bg-success"> Hadith Translation</span></th>
													<th><span class="badge bg-success"> Hadith Number</span></th>
													<th><span class="badge bg-success"> Probability Value</span></th>
												</tr>
											</thead>
											<tbody>
												<?php
												retrieve($proses3);
												?>
											</tbody>
											</table>
										</div>	
									</div>
								<?php
								}else{
									?>
									<div>
										<div class="showback">
											<?php
												//preproses
												$proses1= stopword($keyword);
												$proses2= stemming($proses1);
												echo ('<div class="alert alert-success"> <b>Query Input : '.$keyword.'.</b></div>');
											?>
										</div>
										<div class="content-panel">
											<table class="table table-bordered table-striped table-condensed">
											<thead>
												<tr>
													<th><span class="badge bg-success"> Number</span></th>
													<th><span class="badge bg-success"> Book</span></th>
													<th><span class="badge bg-success"> Chapter</span></th>
													<th><span class="badge bg-success"> Hadith</span></th>
													<th><span class="badge bg-success"> Hadith Translation</span></th>
													<th><span class="badge bg-success"> Hadith Number</span></th>
													<th><span class="badge bg-success"> Probability Value</span></th>
												</tr>
											</thead>
											<tbody>
												<?php
												retrieve($proses2);
												?>
											</tbody>
											</table>
										</div>	
									</div>
								<?php 
								}
							}else {
								?>
								<div class="showback">
									<?php
									echo ('<div class="alert alert-danger"><b>Silahkan Masukkan Query</b></div>');
									?>
								</div>
								<?php
								}
							} 
					?>
				</div>
    		</div><!--/.row-->
		</div>	<!--/.main-->
		
		<script src="js/jquery-1.11.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/chart.min.js"></script>
		<script src="js/chart-data.js"></script>
		<script src="js/easypiechart.js"></script>
		<script src="js/easypiechart-data.js"></script>
		<script src="js/bootstrap-datepicker.js"></script>
		<script src="js/custom.js"></script>
	</body>
</html>