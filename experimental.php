<html>
<head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('includes/master.inc.php');
$db = Database::getDatabase();
$sql = 'select year(t.datum_od) as year,h.prezyvka,count(*) as cnt
from turnaje as t
join zostavy as z on z.turnaj=t.id
join hraci as h on h.id=z.hrac
group by year(t.datum_od),h.id
having cnt>0;';

$sql = 'select year(t.datum_od) as year,h.prezyvka,count(*) as cnt
from turnaje as t
join zostavy as z on z.turnaj=t.id
join (select h.id, h.prezyvka
from hraci as h
left join zostavy as z on h.id=z.hrac
group by h.id
having count(*)>15) as h on h.id=z.hrac
group by year(t.datum_od),h.id;';

$res = $db->query($sql);
while($row = mysql_fetch_assoc($res)) {
  $result[$row['year']][$row['prezyvka']] = $row['cnt'];
  $names []= $row['prezyvka'];
}
$names = array_unique($names);
?>
<meta charset="utf8" />
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
</head>
<body>
<div id="chart_div" style="width: 1000px; height: 800px;"></div>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year'
<?php
	foreach($names as $n)echo ", '$n'";
?>
]
<?php
	foreach($result as $year => $data){
		echo ",['$year'";
		foreach($names as $n){
			if(array_key_exists($n,$data)){
				echo ", ".$data[$n];
			} else {
				echo ", 0";
			} 
		}
		echo "]";
	}
?>
        ]);

        var options = {
          title: 'Turnaje za rok',
          hAxis: {titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0},
	  isStacked: true
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
</script>
<?php

$sql = 'select h1.prezyvka as hr1,h2.prezyvka as hr2,count(*) as cnt
from hraci as h1
join hraci as h2
join zostavy as z1 on h1.id=z1.hrac
join zostavy as z2 on h2.id=z2.hrac and z2.turnaj=z1.turnaj
where h1.id != h2.id
group by h1.id,h2.id
order by count(*) desc;';
$res = $db->query($sql);
//echo '<h1>Silne dvojice</h1><table>';
$h = array();
while($row = mysql_fetch_assoc($res)) {
  if(!isset($h[$row['hr1']])) {
    $h[$row['hr1']] = array($row['hr2'], $row['cnt']);
  }
  //echo "<tr><td>".$row['hr1']."</td><td>".$row['hr2']."</td><td>".$row['cnt']."</td></tr>";
}
//echo '</table>';

echo '<h1>Kto s kym chodi najviac na turnaje</h1>';
echo "<style>#canvas { display: block; margin: auto; width: 1800px; height: 2200px; border: #111 1px solid; overflow: visible; }</style>\n";
echo '<script type="text/javascript" src="js/dracula/vendor/raphael.js"></script>' . "\n";
echo '<script type="text/javascript" src="js/dracula/vendor/jquery-1.8.2.js"></script>' . "\n";
echo '<script type="text/javascript" src="js/dracula/lib/dracula_graffle.js"></script>' . "\n";
echo '<script type="text/javascript" src="js/dracula/lib/dracula_graph.js"></script>' . "\n";
echo '<script type="text/javascript" src="js/dracula/lib/dracula_algorithms.js"></script>' . "\n";
echo '<svg id="canvas"></svg>' . "\n";
echo '<script type="text/javascript">' . "\n";
echo "$(function(){ var g = new Graph();\n";
foreach($h as $h1 => $h2) {
  if($h2[1] < 5)continue;
  echo 'g.addEdge("' . $h1 . '", "' . $h2[0] . '", {"directed": true, "label": ' . $h2[1] . '});' . "\n";
}
echo "redraw = function() { var layouter = new Graph.Layout.Spring(g);\n
  layouter.iterations = 5000;\n
  layouter.layout();\n
  var renderer = new Graph.Renderer.Raphael('canvas', g, 1800, 2200);\n
  renderer.draw();\n
  };\n
  redraw();});\n";
echo "</script>\n";

echo "<table>\n";
foreach($h as $h1 => $h2) {
  echo "<tr><td>" . $h1 . "</td><td>" . $h2[0] . "</td><td>" . $h2[1] . "</td></tr>\n";
}
echo "</table>\n";

if($_GET['id']) {
	$sql = '
select h.id as id, h.prezyvka as hr,count(*) as cnt
from hraci as h
join zostavy as z1 on h.id=z1.hrac
join zostavy as z2 on z2.hrac='.$_GET['id'].' and z2.turnaj=z1.turnaj
join turnaje as t on t.id=z1.turnaj
group by h.id
order by count(*) desc
limit 50;
	';
	$res = $db->query($sql);
	echo '<h1>Best teammates</h1>ordered by mutual tournaments, first line is you<table>';
	while($row = mysql_fetch_assoc($res)) {
        	echo "<tr><td><a href='?id=".$row['id']."'>".$row['hr']."</a></td><td>".$row['cnt']."</td></tr>";
	} 
	echo '</table>';

}

?>
</body>
</html>
