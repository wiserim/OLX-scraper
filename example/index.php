<!DOCTYPE html>
<html>
<head>
    <title>OLX.pl scraper</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>OLX.pl scraper</h1>
    <form action="index.php" method="post">
        URL zapytania:<br>
        <input type="text" name="url" placeholder="URL" required="required"><br>
        Liczba stron:<br>
        <input type="number" name="sites" required="required" value="1"><br>
        <input type="submit">
    </form>

<?php
include_once 'src/OLXscraper.php';

if(isset($_POST['url']) && isset($_POST['sites'])) {
    if (filter_var($_POST['url'], FILTER_VALIDATE_URL) === false) {
        echo($_POST['url']." nie jest prawidłowym adresem url");
    }
    else {     
        $list = OLXscraper::getOLXListData($_POST['url'], $_POST['sites']);
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
        $i = 1;
        foreach ($list as $element) {
            echo "<tr><td>".$i.".</td><td>".$element['title']."</td><td>".$element['user']."</td><td>".$element['location']."</td><td>".$element['phone']."</td></tr>";
            $i++;
        }
        echo "</tbody></table>";
    }
}
?>
</body>
</html>
