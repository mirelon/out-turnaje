<?php               
	require 'includes/master.inc.php';
	$nav = 'turnaje';

	$turnaj = new Turnaj($_GET['id']);
	if(!$turnaj->ok()) {
	  redirect('turnaje.php');
  } 
  
  if(isset($_GET["od"])) $get_od = $_GET["od"];
  else $get_od = null;
  
  if(isset($_GET["do"])) $get_do = $_GET["do"];
  else $get_do = null;
  
  include('inc/header.inc.php');

?>

  <script type="text/javascript">  
    
		$(function(){						
			$('select#od, select#do').selectToUISlider({
				labels: 0,
        tooltipSrc: 'value',
        sliderOptions: { 
          animate: true, 
          change: function(event) { 
            rozsah.submit(); 
          } 
        }  
			});						
		});	        		
    	      
	</script>
  
  <script type="text/javascript"> 
    
    $(document).ready(function(){ 
    
      $("#tabulkaZostava").tablesorter({ 
        // sort on the first column and third column, order asc 
        sortList: [[0,0]], 
        widgets: ['zebra'],
        widthFixed: true,
        cancelSelection: true,
        headers: { 
                5: {sorter:'umiestnenie'} 
            } 
      })
      
    }); 
    
	</script>

  <script language="JavaScript">
    function potvrd() {
      return confirm("Naozaj chceš zmazať tento turnaj?")
    }
  </script>
  
  <form name="rozsah" action="turnaj.php" method="get">
      <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"	/>							
			<select name="od" id="od" style="display:none;">          
      <?php
        $zaciatok_m = najstarsi_turnaj_mesiac();        
        $zaciatok_r = najstarsi_turnaj_rok();
        $koniec_m   = date("m");
        $koniec_r   = date("Y");                
          
        for($rok=$zaciatok_r; $rok <= $koniec_r; $rok++) {
          echo '<optgroup label="'.$rok.'">';
          
          if($rok == $zaciatok_r) $mesiac_od = $zaciatok_m;
          else $mesiac_od = 1;
          
          if($rok == $koniec_r) $mesiac_do = $koniec_m;
          else $mesiac_do = 12;
          
          for($mesiac=$mesiac_od; $mesiac<= $mesiac_do; $mesiac++) {
            $value = str_pad($mesiac, 2, '0', STR_PAD_LEFT).'.'.$rok;
            echo '<option value="'.$value.'"';
            if((is_null($get_od)) && ($rok == $zaciatok_r) && ($mesiac == $zaciatok_m)) echo ' selected="selected"';
            elseif((!is_null($get_od)) && ($get_od == $value)) echo ' selected="selected"';          
            echo '></option>';
          }
          echo '</optgroup>';
        }      
      ?>
      </select>
      
      <select name="do" id="do" style="display:none;">          
      <?php               
        $zaciatok_m = najstarsi_turnaj_mesiac();        
        $zaciatok_r = najstarsi_turnaj_rok();
        $koniec_m   = date("m");
        $koniec_r   = date("Y");        
        $selected   = false;
        for($rok=$zaciatok_r; $rok <= $koniec_r; $rok++) {
          echo '<optgroup label="'.$rok.'">';
          
          if($rok == $zaciatok_r) $mesiac_od = $zaciatok_m;
          else $mesiac_od = 1;
          
          if($rok == $koniec_r) $mesiac_do = $koniec_m;
          else $mesiac_do = 12;
          
          for($mesiac=$mesiac_od; $mesiac<= $mesiac_do; $mesiac++) {
            $value = str_pad($mesiac, 2, '0', STR_PAD_LEFT).'.'.$rok;
            echo '<option value="'.$value.'"';
            if((!is_null($get_do)) && ($get_do == $value)) {
              echo ' selected="selected"';
              $selected = true;
            }
            if((!$selected) && ($rok == $koniec_r) && ($mesiac == $koniec_m)) echo ' selected="selected"';
            echo '></option>';
          }
          echo '</optgroup>';
        }      
      ?>
      </select>		                         														
	</form> 
  
  <?php     
    if($Auth->loggedIn()):
      echo 'Administrácia: '.$turnaj->link_zmen().' | '.$turnaj->link_zmen_zostavu().' | '.$turnaj->link_zmaz().'<br />';
    endif;
  ?>
  
  <h3>Vizitka turnaja: <?php echo $turnaj->link($turnaj->turnaj, $get_od, $get_do); ?></h3> 
  
  <?php
    echo 'Kategória: '.$turnaj->nazov_kategorie().'<br/>';
    echo 'Kde: '.$turnaj->mesto.' ('.$turnaj->stat.')<br/>';
    echo 'Kedy: '.$turnaj->datum_format().'<br/>';
    if($turnaj->tim_Out > 0) { 
      echo 'Tím: '.$turnaj->nazov_timu().'<br/>';
    }
    echo 'Výsledok: '.$turnaj->vysledok.'. miesto z '.$turnaj->pocet_timov.' tímov<br/>';
    echo 'Spirit: '.$turnaj->spirit_slovne().'<br/>';
    if($turnaj->report != "") { 
      echo 'Report: '.make_link($turnaj->report, $turnaj->report, '_blank').'<br/>';
    }
  ?>
  
  <h3>Zostava</h3>  

  <table border="0" cellpadding="0" cellspacing="1" id="tabulkaZostava" class="tablesorter">
    <thead>
      <tr>
			  <th>Prezývka</th>
				<th>Meno</th>
				<th>Priezvisko</th>
				<th>Domovský tím</th>      
				<th width="70px">Turnajov</th>
        <th>Priemer výsledkov</th>
        <th>Pokorené tímy</th>
        <th width="60px">Spiritov</th>
      </tr>
    </thead>
    <tbody>
		  <?php 
        $zostava = $turnaj->zostava();
        
        foreach($zostava as $z) {
          $hrac = new Hrac($z);
      	  if($hrac->ok()) {
      	    echo $hrac->riadok_tabulky($get_od, $get_do);
      	  }
        }
      ?>			
    </tbody>
  </table>
  
  <h3>Zápasy</h3>  
  
  <h3>Štatistiky</h3> 
  
  <?php
  /*
//BASIC CHART:
	$data=array('4,6,21,7,1,6,17,5,2,1,7,9'); //note arrays don't have | pipes in them
	$moo=new googleChart($data);
	$moo->draw();


//CHART WITH LABEL:
	$moo= new googleChart('4,6,21,7,1,6,17,5,2,1,7,9');
	$moo->setLabels('june|july|aug','bottom');
	$moo->draw();

//CHART BASIC WITH LABELS
	$chart1= new googleChart('4,1,6,8,1','pie','foods','300x200');
	$chart1->setLabels('cake|pie|muffins|cookies|icecream');
	$chart1->draw();
	
	
//CHART WITH LABEL AND MIN/MAX VALUES ON RIGHT
	$moo= new googleChart('4,6,21,7,1,6,17,5,2,1,7,9');
	$moo->setLabelsMinMax(5,'right'); //call before other funcs that make labels
	$moo->setLabels('june|july|aug','bottom');
	$moo->draw();

//PIE CH//ART WITH LABELS
	$data='4,23,65';
	$moo= new googleChart($data,'pie');
	$moo->setLabels('cows|dogs|peas');
	$moo->draw(true);
	
	//or an alternative to above 'pie' you can use $moo->setType('pie');

//CHART WITH 3 DIFFERENT DATA SETS using smartDataLabel(), A LEGEND AND MINMAX LABELS
	$data=array(
		'cows'=>array(4,5,6,7,9),
		'dogs'=>array(6,1,4,2,6),
		'peas'=>array(5.4,9,1,6,2)
	);
	$moo= new googleChart();
	$moo->smartDataLabel($data);
	$moo->setLabelsMinMax(5,'left'); 
	$moo->draw(true);

//CHART USING NEGATIVE NUMBERS
	$chart=new googleChart(null,$chartType); //don't load data yet
	$chart->negativeMode=true; //set negative mode first
	$chart->loadData($chartData); //then load data
	$chart->setLabelsMinMax();
	$chart->draw();

//CHART WITH LOTS OF OPTIONS, data sets are supplied in a string 
	$data='1,4,6,8,2|3,7,8,3,2';
	$chart1=new googleChart($data);
	$chart1->dimensions='400x125';
	$chart1->fillColor='76A4FB';
	$chart1->setLabelsMinMax();
	$chart1->legend='Ammount of cookies|Raisens';
	$chart1->setLabels('jan|feb|march|apr|may','bottom');
	$chart1->setLabels('+low|mid|top','left');
	$chart1->showGrid=1;
	$chart1->draw();

//CHART USING NEGATIVE NUMBERS AND GRID
	$month='May';
	$daysInMonth=31;
	$chartDates='';
	for ($i=1;$i<=$daysInMonth;$i++) {
		if ($chartDates) $chartDates.='|';
		$chartDates.=$i;
	}
	$chartData='0,0,-109.9,0,0,0,-12310.58,-478.5,0,0,-473.38,0,0,0,-398,0,0,0,0,0,-134.6,-1513.1,0,0,-454.26,0,0,0,0,-1258.04,-2237.8';
	$chart=new googleChart(null,'line','Expenses','700x250');
	$chart->negativeMode=true; //values are negative
	$chart->loadData($chartData);
	$chart->barWidth=10; //makes bars thinner
	$chart->setLabelsMinMax();
	$chart->setLabels($chartDates,'bottom');
	$chart->setLabels($month,'bottom');
	$chart->showGrid=1;
	$chart->draw();        */
                      ?>
  <?php include('inc/footer.inc.php'); ?>
