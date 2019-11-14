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
		<title>Home</title>

		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/datepicker3.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">

		<!--Theme Switcher-->
		<style id="hide-theme">
			body{
				display:none;
			}
		</style>
		<style>
			.center {
				display: block;
				margin-left: auto;
				margin-right: auto;
				width: 50%;
			}
			/* Container holding the image and the text */
			.container {
			position: relative;
			}

			/* Bottom right text */
			.text-block {
			position: absolute;
			bottom: 20px;
			right: 20px;
			background-color: black;
			color: white;
			padding-left: 20px;
			padding-right: 20px;
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
						<a class="navbar-brand" href="index.php"><span>Query Answering</span> System</a>
				</div>
			</div><!-- /.container-fluid -->
		</nav>
		<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
			<div>
				<img src="images/qas_hadith_icon.png" width="180" class="img-responsive" alt="">
			</div>
			<div class="divider"></div>
			<ul class="nav menu">
				<li class="active"><a href="index.php"><em class="fa fa-home">&nbsp;</em> Home</a></li>
				<li><a class="" href="search.php"><span class="fa fa-desktop">&nbsp;</span> Query Answering System</a></li>
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
						<em class="fa fa-home"></em>
					</a></li>
					<li class="active">Home</li>
				</ol>
			</div><!--/.row-->
			
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-primary">
						<div class="panel panel-heading">Home</div>
					</div>
				</div>
			</div><!--/.row-->
			
			<div class="panel panel-container">
				<div class="row">
					<div class="col-xs-6 col-md-6 no-padding">
						<div class="panel panel-teal panel-widget border-right">
							<div class="row no-padding"><em class="fa fa-xl fa-book color-blue"></em>
								<div class="large">
									<?php
										$sql = mysqli_query($connect, "SELECT * FROM tb_hadis");
										$jmlData = mysqli_num_rows($sql);
										echo $jmlData;
									?>
								</div>
								<div class="text-muted">Hadith Data</div>
							</div>
						</div>
					</div>
					<div class="col-xs-6 col-md-6 no-padding">
						<div class="panel panel-blue panel-widget border-right">
							<div class="row no-padding"><em class="fa fa-xl fa-book color-orange"></em>
								<div class="large">
									<?php
										$sql = mysqli_query($connect, "SELECT * FROM tb_tes");
										$jmlData = mysqli_num_rows($sql);
										echo $jmlData;
									?>
								</div>
								<div class="text-muted">Thesaurus Data</div>
							</div>
						</div>
					</div>
				</div><!--/.row-->
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">QAS Hadith</div>
						<div class="panel-body">
							<h4><b>What is QAS Hadith?</b></h4>
							<p><br>QAS Hadits (Query Answering System of Hadith) is an information retrieval that used to searching data hadith, this system has implement the Naive Bayes Method. This system is provide searching hadith data without expansion query or with expansion query from indonesia thesaurus.</p>
							<h4><b><br>System Design</b></h4>
							<br>
							<img src="images/system_design.png" class="center">
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
		<script src="js/custom.js"></script>
			
	</body>
</html>