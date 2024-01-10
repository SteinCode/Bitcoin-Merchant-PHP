<html>
<head>
	<title>Sample Merchant client</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class = "logo-container"> <a class = "logo-url" href="https://spectrocoin.com" target="_blank"> <img class = "logo-img" src = "spectrocoin-logo.svg"></a></div>
	<div class = "main-section">
		<p>
			This is a sample of <a href="https://spectrocoin.com" target="_blank">SpectroCoin.com</a> Merchant API usage.
		</p>
		<form method="post" class="form-container">
			<div class="form-row">
				<label class="form-label" for="project_id">Project ID:</label>
				<div class="form-input">
					<input type="text" id="project_id" name="project_id">
				</div>
			</div>
			<div class="form-row">
				<div class="form-label"></div> <!-- Empty div for alignment -->
				<input class = "save_button" type="submit" value="Save">
			</div>
		</form>


		<ol>
			<li><a href="direct.php">Create merchant order (direct API result usage)</a></li>
			<li><a href="redirect.php">Create merchant order (redirect to payment page)</a></li>
		</ol>
	</div>
</body>
</html>
