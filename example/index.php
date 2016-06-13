<!DOCTYPE html>
<html>
<head>
    <title>OLX scraper</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>OLX scraper</h1>
        <form action="index.php" method="post">
            <label for="url">URL zapytania:</label>
            <input class="url" type="text" name="url" placeholder="URL" required="required"><br>
            <label for="stronyOd">Strony:</label>
            <input class="numer" type="number" name="pageFrom" required="required" value="1"> -
            <input class="numer" type="number" name="pageTo" required="required" value="1"><br>
			<input type="checkbox" name="unique">Ignoruj duplikaty nr telefonów<br>
            <input class="submit" type="submit">
        </form>

<?php
include_once 'src/OLXscraper.php';

if(isset($_POST['url']) && isset($_POST['pageFrom']) && isset($_POST['pageTo'])) {
    if (filter_var($_POST['url'], FILTER_VALIDATE_URL) === false) {
        echo($_POST['url']." nie jest prawidłowym adresem url");
    }
	else if($_POST['pageFrom']>$_POST['pageTo']){
		echo("Podano nieprawidłowy przedział");
	}
    else {
		set_time_limit(999999999);
        error_reporting (E_ERROR);
		if(isset($_POST['unique'])){
			echo'<p>Włączono ignorowanie duplikatów nr telefonów</p>';
			$list = OLXscraper::getOLXListData($_POST['url'], $_POST['pageFrom'], $_POST['pageTo'], $_POST['unique']);
		}
		else $list = OLXscraper::getOLXListData($_POST['url'], $_POST['pageFrom'], $_POST['pageTo'], false);
		echo'<p>Dane zapisano w pliku "wynik.csv"</p>';
		echo "<table>
            <thead>
                <tr>
                    <th>Nr</th>
                    <th>Nazwa ogłoszenia</th>
                    <th>Użytkownik</th>
                    <th>Miejscowość</th>
                    <th>Nr telefonu</th>
                </tr>
            </thead>
            <tbody>";
        $file =fopen("wynik.csv", "w");
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        $header = array('Nazwa ogłoszenia', 'Użytkownik', 'Miejscowość', 'Nr telefonu');
        fputcsv($file, $header, ';', '"');
		$i = 1;
        foreach ($list as $element) {
			fputcsv($file, $element, ';', '"');
            echo "<tr><td>" . $i . ".</td><td>" . $element['title'] . "</td><td>" . $element['user'] . "</td><td>" . $element['location'] . "</td><td>" . $element['phone'] . "</td></tr>";
            $i++;
        }
        echo "</tbody></table>";
        fclose($file);
    }
}
?>
</body>
</html>
