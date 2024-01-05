<html>
<head>
	<title>Sample Merchant client</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class = "main-section">
		<p>
			This is a sample of <a href="https://spectrocoin.com" target="_blank">SpectroCoin.com</a> Merchant API usage.
		</p>

		<!-- Form for entering credentials -->
		<form method="post" action="saveCredentials.php">
			Project ID: <input type="text" name="project_id" onchange="saveCredentials()"><br>
			Private Key: <textarea name="private_key" onchange="saveCredentials()"></textarea><br>
			<input type="submit" value="Save">
		</form>

		<ol>
			<li><a href="direct.php">Create merchant order (direct API result usage)</a></li>
			<li><a href="redirect.php">Create merchant order (redirect to payment page)</a></li>
		</ol>
	</div>
</body>
</html>
