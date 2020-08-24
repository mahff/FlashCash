<?php // content="text/plain; charset=utf-8"
  require_once ('../jpgraph/src/jpgraph.php');
  require_once ('../jpgraph/src/jpgraph_bar.php');
  session_start();

  define("DB_HOST", "10.40.128.23");
  define("DB_USER", "y2018l3i_mamhiyen");
  define("DB_PASSWORD", "Fjk45tv*");
  define("DB_DATABASE", "db2018l3i_mamhiyen");
  $conn_string = "host=".DB_HOST. " port=5432 dbname=".DB_DATABASE." user=".DB_USER." password=".DB_PASSWORD;
  $dbconn4 = pg_connect($conn_string);

  if(isset($_SESSION["id"]) && isset($_SESSION["login"])) {
    $query= pg_query("SELECT stock FROM machine");
    $data1y=array();
    while($row=pg_fetch_array($query, null, PGSQL_ASSOC)){
      $data1y[]=$row['stock'];
    }

  // Create the graph. These two calls are always required
  $graph = new Graph(450,300,'auto');
  $graph->SetScale("textlin");

  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);

  $graph->yaxis->SetTickPositions(array(0,30,60,90,120,150,200,500), array(15,45,75,105,135));
  $graph->SetBox(false);

  $graph->ygrid->SetFill(false);
  $graph->xaxis->SetTickLabels(array('Machine 1','Machine 2','Machine 3'));
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);
  $graph->yaxis->title->Set("Stock");

  // Create the bar plots
  $b1plot = new BarPlot($data1y);


  // Create the grouped bar plot
  $gbplot = new GroupBarPlot(array($b1plot));
  // ...and add it to the graPH
  $graph->Add($gbplot);

  //color of bar plot
  $b1plot->SetColor("white");
  $b1plot->SetFillColor("#cc1111");


  $graph->title->Set("Graphique affichant le stock des machines");

  // Display the graph
  $graph->Stroke();
}
?>
