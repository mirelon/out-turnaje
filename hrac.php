<?php               
	require 'includes/master.inc.php';
	$nav = 'hraci';

	$hrac = new Hrac($_GET['id']);
	if(!$hrac->ok()) {
	  redirect('hraci.php');
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
    
      $("#tabulkaTurnajov").tablesorter({          
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
  
  <script language="JavaScript">
    function potvrd() {
      return confirm("Naozaj chceš zmazať tohto hráča?")
    }
  </script>
  
  <form name="rozsah" action="hrac.php" method="get">
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
      echo 'Administrácia: '.$hrac->link_zmen().' | '.$hrac->link_zmaz().'<br />';
    endif;
  ?>
  
  <h3>Vizitka hráča: <?php echo $hrac->link($hrac->prezyvka, $get_od, $get_do); ?> (<?php echo $hrac->meno." ".$hrac->priezvisko; ?>)</h3> 
  
  <?php
    if($hrac->externe) { 
      echo 'Domáci tím: '.$hrac->domaci_tim.'<br/>';
    }
    if($hrac->profil != "") { 
      echo 'Profil na stránke: '.make_link($hrac->profil, $hrac->profil, '_blank').'<br/>';
    }
    echo 'Turnajov: '.$hrac->pocet_turnajov($get_od, $get_do).'<br/>';
    echo 'Priemer výsledkov: '.$hrac->priemerne_umiestnenie($get_od, $get_do).' z '.$hrac->priemerny_pocet_timov($get_od, $get_do).'<br/>';
    echo 'Spiritov: '.$hrac->pocet_spiritov($get_od, $get_do).'<br/>';
      
  ?>
  
  <h3>Turnaje</h3> 

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
        $turnaje = $hrac->turnaje($get_od, $get_do);
        
        foreach($turnaje as $t) {
          $turnaj = new Turnaj($t);
      	  if($turnaj->ok()) {
      	    echo $turnaj->riadok_tabulky($get_od, $get_do);
      	  }
        }
        
      ?>			
    </tbody>
  </table> 
  
  <h3>Štatistiky</h3>
  <center> 
  <?php
  /*
  echo 'Počet turnajov: '.$hrac->pocet_turnajov().'<br/>';
  echo 'Počet spiritov: '.$hrac->pocet_spiritov().'<br/>';
  echo 'Priemerné umiestnenie: '.$hrac->priemerne_umiestnenie().' z '.$hrac->priemerny_pocet_timov().' tímov<br/>';
  
  echo 'Najčastejší spoluhráči: '; 
  $spoluhraci = $hrac->najcastejsi_spoluhraci(6);
  
  foreach($spoluhraci as $k => $v) {
  	$spoluhrac = new Hrac($k);
    echo $spoluhrac->link($spoluhrac->prezyvka).' '.$v.'<br/>';
  }   

  
  
  
  $data= $hrac->pocet_turnajov($get_od, $get_do).','.(pocet_turnajov($get_od, $get_do) - $hrac->pocet_turnajov($get_od, $get_do));
	$moo= new googleChart($data,'pie','účasť na turnajoch');
	$moo->setLabels('bol|nebol');
	$moo->draw(true); 
		
	$umiestnenia = $hrac->umiestnenia(true, true, $get_od, $get_do);
  $pocty_timov = $hrac->pocty_timov(true, true, $get_od, $get_do);    
  $data = '';
  
  foreach($umiestnenia as $k=>$v) {
    $hodnota = 1 - ($umiestnenia[$k]/$pocty_timov[$k]);
    $data .= $hodnota.',';
  }	 
   
	$moo= new googleChart($data);
  $moo->showGrid=1;
	$moo->setLabels('2002|2011','bottom');
	$moo->setLabels('posledný|prvý','left');
	$moo->draw();    
    */  
?>	
	      </center>
  <?php include('inc/footer.inc.php'); ?> 