<?php
	$servername = "localhost";
	$username = "turkeysandwich";
	$password = "cranberrysauce";
	$database = "turkeysandwich";

	// Create the connection
	$conn = new mysqli($servername, $username, $password, $database);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	if (isset($_POST["approve"])) {
		$status = "approve";
	} else {
		$status = "deny";
	}

	if ($status == "approve" || $status == "deny") {
		foreach ($_POST as $key => $val) {
			if (substr($key, 0, 3) == "cb_") {
				$topic = substr($key, 3);
				$comment = $_POST["cm_" . $topic];
				$email_to = $_POST["hd_" . $topic];
				$subject = "Topic Approval System";
				$email_from = "rmkics@rit.edu";
				$topic = str_replace("_", " ", substr($key, 3));
				$sql = "UPDATE topic SET status = '" . $status . "'" .
				" WHERE proposed_topic = '" . $topic . "';";

				if ($conn->query($sql) === TRUE) {
					$body = "Your topic has been $status with reason $comment.";
					mail($email_to, $subject, $body, "From: <$email_from>");
				} else {
					echo "Failure<br/>";
					echo $sql;
				}
			}
		}
	}
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang=""> 
<!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <form class="navbar-form navbar-right" role="form">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h2>Pending Topics For Approval</h2>
	<form method="POST" id="pending_form" action="eval_topics.php">
	<table id="pending_topics" class="table table-striped table-bordered">
		<thead>
		<tr>
			<th>Status</th>
			<th>RIT DCE</th>
			<th>Email</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Topic</th>
			<th>Ref Link</th>
			<th>Time Submitted</th>
			<th>Comments</th>
		</tr>
		</thead>
		<tbody><?php
	$sql = "SELECT status, studentID, student_firstname, student_lastname, proposed_topic, reference_link, time_submitted FROM topic WHERE status = 'pending';";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
		?><tr>
			<td><input type="checkbox" id="cb_<?= $row["proposed_topic"]?>" name="cb_<?= $row["proposed_topic"]?>"></td>
			<td><?= $row["studentID"]?></td>
			<td><?= $row["studentID"]?>@g.rit.edu</td>
			<td><?= $row["student_firstname"]?></td>
			<td><?= $row["student_lastname"]?></td>
			<td><?= $row["proposed_topic"]?></td>
			<td><?= $row["reference_link"]?></td>
			<td><?= $row["time_submitted"]?></td>
			<td><input type="text" id="cm_<?= $row["proposed_topic"]?>" name="cm_<?= $row["proposed_topic"]?>"></td>
			<input type="hidden" id="hd_<?= $row["proposed_topic"]?>" name="hd_<?= $row["proposed_topic"]?>" value="<?= $row["studentID"]?>@g.rit.edu">
		</tr><?php
		}
	}
		?></tbody>
	</table>
	<button id="approve" type="submit" class="btn btn-default" aria-label="left-align" name="approve">Approve</button>
	<button id="deny" type="submit" class="btn btn-default" aria-label="left-align" name="deny">Deny</button>
	</form>
	<h3>Topics</h3>
	<table id="all_topics" class="table table-striped table-bordered">
		<thead>
		<tr>
			<th>DCE RIT</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Topic</th>
			<th>Status</th>
			<th>Project 1 URL</th>
		</tr>
		</thead>
		<tbody><?php
	$sql = "SELECT studentID, student_firstname, student_lastname, proposed_topic, status FROM topic";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
		?><tr>
			<td><?= $row["studentID"]?></td>
			<td><?= $row["student_firstname"]?></td>
			<td><?= $row["student_lastname"]?></td>
			<td><?= $row["proposed_topic"]?></td>
			<td><?= $row["status"]?></td>
			<td>kelvin.ist.rit.edu/~<?= $row["studentID"]?>/</td>
		</tr><?php
		}
	}
		?></tbody>
	</table>
       </div>
    </div>

    <div class="container">
     </div>

      <hr>

      <footer>
        <p>&copy; Company 2015</p>
      </footer>
    </div> <!-- /container -->        
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

        <script src="js/main.js"></script>
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">
	<script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X','auto');ga('send','pageview');
        </script>
	<script>
		$(document).ready(function() {
			$("#topics").dataTable();
		});
	</script>
    </body><?php>
	$conn->close();
?></html>
