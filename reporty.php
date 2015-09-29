<?php
require_once("../wp-config.php");
$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) 
  or die("Unable to connect to MySQL<br/>");
  echo "Connected to Wordpress DB<br/>";
  $db = mysql_select_db(DB_NAME,$dbh) 
  or die("Could not select examples");

echo "Selecting post counts in category Turnaje<br/>";
$sql = "select u.display_name, count(p.ID) as cunt, max(p.post_date) as last_post, sum(length(p.post_content)) as total from wp_posts as p join wp_users as u on p.post_author = u.ID join wp_term_relationships as t on p.ID = t.object_id where t.term_taxonomy_id = 4 group by u.ID order by cunt desc, last_post desc;";
$res = mysql_query($sql);
echo "<table border='1' cellspacing='0'>";
echo "<tr><td>Meno</td><td>Kolko</td><td>Posledny</td><td>Celkovy pocet znakov</td></tr>";
while($row = mysql_fetch_array($res)) {
	echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td align='right'>$row[3]</td><td>";
	for($i=0;2000*$i<intval($row[3]);$i++) {
		echo "▪";
	}
	echo "</td></tr>";
}
echo "</table>";

echo "Selecting post titles<br/>";
$sql = "select u.display_name, p.post_date, p.post_title, length(p.post_content), p.post_status from wp_posts as p join wp_users as u on p.post_author = u.ID join wp_term_relationships as t on p.ID = t.object_id where t.term_taxonomy_id = 4 order by u.ID;";
$res = mysql_query($sql);
echo "<table border='1' cellspacing='0'>";
echo "<tr><td>Meno</td><td>Datum</td><td>Turnaj</td><td>Pocet znakov</td><td>Status</td></tr>";
while($row = mysql_fetch_array($res)) {
        echo "<tr><td>$row[0]</td><td>$row[1]</td>";
	echo "<td>$row[2]</td>";
	echo "<td align='right'>$row[3]</td><td>$row[4]</td><td>";
	for($i=0;1000*$i<intval($row[3]);$i++) {
		echo "▪";
	}
	echo "</td></tr>";
}
echo "</table>";

?>
