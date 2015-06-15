<?php include('survey.php'); ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="survey.css"></head>
<body>

<?php echo survey_name('sample-survey') ?>

<?php survey_conduct('sample-survey') ?>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['survey'])): ?>
<hr>
<?php survey_summarize('sample-survey') ?>
<?php endif; ?>

</body>

</html>

