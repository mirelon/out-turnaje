<?php               
	require 'includes/master.inc.php';
	$nav = 'zapasy';

	$zapas = new Zapas($_GET['id']);
	if(!$zapas->ok()) {
	  redirect('zapasy.php');
  } 
  
  $turnaj = new Turnaj($zapas->turnaj);
  if(!$turnaj->ok()) {
	  redirect('zapasy.php');
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
  

  <script language="JavaScript">
    function potvrd() {
      return confirm("Naozaj chceš zmazať tento zápas?")
    }
  </script>
  
  <form name="rozsah" action="zapas.php" method="get">
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
      echo 'Administrácia: '.$zapas->link_zmen().' | '.$zapas->link_zmaz().'<br />';
    endif;
  ?>
  
  <h3>Vizitka zápasu: <?php echo $zapas->link("Outsiterz vs. ".$zapas->super, $get_od, $get_do); ?> na turnaji <?php echo $turnaj->link($turnaj->turnaj, $get_od, $get_do); ?></h3> 
  
  <?php
    echo 'Výsledok: '.$zapas->bodov_Out.':'.$zapas->bodov_super.'<br/>';    
  ?>  
 
  <?php include('inc/footer.inc.php'); ?>