<?php               
	require 'includes/master.inc.php';
	$nav = 'index';
  
  if(isset($_GET["od"])) $get_od = $_GET["od"];
  else $get_od = null;
  
  if(isset($_GET["do"])) $get_do = $_GET["do"];
  else $get_do = null;
  
  $query = "SELECT * FROM turnaje";
                      
  if(!is_null($get_od)) {
    $query .= make_datum_od_query("WHERE datum_od", $get_od);
  }
  else {    
    $query .= " WHERE datum_od >= '0000-00-00'";
  }
              
  $query .= make_datum_do_query("AND datum_do", $get_do);
  $query .= " ORDER BY datum_zapisu DESC, datum_od DESC LIMIT 0, 5";
    
	$turnaje = DBObject::glob('Turnaj', $query);
  
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
    
      $("#tabulkaTurnajov").tablesorter({ 
        // sort on the first column and third column, order asc 
        sortList: [[4,1]], 
        widgets: ['zebra'],
        widthFixed: true,
        cancelSelection: true,
        headers: { 
                4: {sorter:'ownDate'}, 
                5: {sorter:'umiestnenie'} 
            } 
      })
      
    }); 
    
	</script>
  
  <form name="rozsah" action="index.php" method="get">      							
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
  
  <h3>Databáza turnajov Outsiterz</h3> 
  
  <?php
    $turnajov = pocet_turnajov($get_od, $get_do);
    $zapasov  = pocet_zapasov($get_od, $get_do);
    /*$hracov  = pocet_hracov($get_od, $get_do);*/
    switch($turnajov)
    {
      case 1:
        echo 'V databáze je za zvolené obdobie 1 '.make_link_turnaje('turnaj', $get_od, $get_do).'.<br />'; 
        break;
      case 2:
      case 3:
      case 4:
        echo 'V databáze sú za zvolené obdobie '.$turnajov.' '.make_link_turnaje('turnaje', $get_od, $get_do).'.<br />'; 
        break;
      default:
        echo 'V databáze je za zvolené obdobie '.$turnajov.' '.make_link_turnaje('turnajov', $get_od, $get_do).'.<br />'; 
        break;
    }
    
    echo ' Priemerne sme sa na nich umiestnili '.priemerne_umiestnenie($get_od, $get_do).' z '.priemerny_pocet_timov($get_od, $get_do).' tímov';
    echo ' a získali sme '.pocet_spiritov($get_od, $get_do).' spiritov.<br /><br />';
        
  ?>
  
  Naposledy boli pridané tieto turnaje:<br />
  <table border="0" cellpadding="0" cellspacing="1" id="tabulkaTurnajov" class="tablesorter">
    <thead>
      <tr>
			  <th>Názov</th>
        <th width="76px">Kategória</th>
				<th>Mesto</th>
				<th width="40px">Štát</th>
				<th>Dátum</th>
        <th width="55px">Miesto</th>
        <th width="48px">Spirit</th>	
      </tr>
    </thead>
    <tbody>
		  <?php 
        foreach($turnaje as $t) {
          echo $t->riadok_tabulky($get_od, $get_do);
        }
      ?>			
    </tbody>
  </table> 
    
  <?php include('inc/footer.inc.php'); ?>