<?php
	require 'includes/master.inc.php';
	$nav = 'hraci';
  
  $query = "SELECT * FROM hraci";
  
  if(isset($_GET["od"])) $get_od = $_GET["od"];
  else $get_od = null;
  
  if(isset($_GET["do"])) $get_do = $_GET["do"];
  else $get_do = null;
			
	$hraci = DBObject::glob('Hrac', $query);
	
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
    
      $("#tabulkaHracov").tablesorter({ 
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
  
  <form name="rozsah" action="hraci.php" method="get">								
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
  
  <table border="0" cellpadding="0" cellspacing="1" id="tabulkaHracov" class="tablesorter">
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
        foreach($hraci as $h) {
          if($h->pocet_turnajov($get_od, $get_do) > 0)
            echo $h->riadok_tabulky($get_od, $get_do);
        }
      ?>			
    </tbody>
  </table> 
  
<?php include('inc/footer.inc.php'); ?>
