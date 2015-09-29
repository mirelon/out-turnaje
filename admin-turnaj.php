<?php
//TODO: zmazat zmienku o turnaji aj zo zostav
//validne linky na report?
//overenie, ci je vysledok<=pocettimov
  
	require 'includes/master.inc.php';
  $nav = 'admin-turnaj';
    
	$Auth->requireUser('login');
	
  if((!isset($_GET['akcia'])) || ($_GET['akcia'] == '')) $akcia = 'novy';
  else $akcia = $_GET['akcia'];

  //novy turnaj
  if(($akcia == 'novy') || ($akcia == 'zmen')) {
  	if(isset($_POST['btnPridajTurnaj']))
  	{
  		$Error->blank($_POST['turnaj'], 'turnaj', 'Turnaj');
      $Error->blank($_POST['mesto'], 'mesto', 'Mesto');
      $Error->blank($_POST['stat'], 'stat', 'Štát');
      $Error->date($_POST['datum_od'], 'datum_od');
      $Error->date($_POST['datum_do'], 'datum_do');
      $Error->compare_date($_POST['datum_od'], $_POST['datum_do'], 'datum_od');  
  				
  		if($Error->ok())
  		{
  			if($akcia == 'novy') $t = new Turnaj();
        else $t = new Turnaj($_GET['id']);
                
  			$t->turnaj       = $_POST['turnaj'];
        $t->kategoria    = $_POST['kategoria'];
        $t->datum_od     = dater($_POST['datum_od'], 'Y-m-d');
        $t->datum_do     = dater($_POST['datum_do'], 'Y-m-d');
        $t->vysledok     = $_POST['vysledok'];
        $t->pocet_timov  = $_POST['pocet_timov'];
        $t->tim_Out      = $_POST['tim_Out'];
        $t->report       = $_POST['report'];
        $t->mesto        = $_POST['mesto'];
        $t->stat         = $_POST['stat'];
        $t->tim_Out      = $_POST['tim_Out']; 
        if(isset($_POST['spirit'])) $t->spirit = $_POST['spirit']; 
        else $t->spirit = 0;       
  			
        if($akcia == 'novy') {
          $t->datum_zapisu = dater(null, 'Y-m-d');
          $t->insert();
          //presmerujeme na pridanie zostavy
          redirect('admin-zostava.php?id='.$t->id);
        }
        else {
          $t->update();
          //presmerujeme na stranku turnaja
          redirect('turnaj.php?id='.$t->id);          
        }
        
  		}
  		else
  		{
  			$turnaj      = $_POST['turnaj'];  
        $kategoria   = $_POST['kategoria'];         		
        $datum_od    = $_POST['datum_od'];
        $datum_do    = $_POST['datum_do'];		  
        $vysledok    = $_POST['vysledok'];
        $pocet_timov = $_POST['pocet_timov'];
        $tim_Out     = $_POST['tim_Out'];
        $report      = $_POST['report'];      
        $mesto       = $_POST['mesto'];
        $stat        = $_POST['stat'];
        $tim_Out     = $_POST['tim_Out'];
        if((isset($_POST['spirit'])) && ($_POST["spirit"] == 1)) $spirit = " checked";
        else $spirit = ""; 
  		}
  	}
  	elseif($akcia == 'novy')
  	{
  	    $turnaj      = '';
        $kategoria   = '';  	
        $datum_od    = '';
        $datum_do    = '';
        $vysledok    = '';
        $pocet_timov = '';  
        $tim_Out     = '';
        $report      = 'http://www.outsiterz.org/?p=';
        $mesto       = '';
        $stat        = 'SK';
        $tim_Out     = ''; 
        $spirit      = '';     
  	}
  	else {
  	    //vytiahneme udaje o menenom turnaji
  	    $t = new Turnaj($_GET['id']);
  	    $id           = $t->id; 
  	    $turnaj       = $t->turnaj;
        $kategoria    = $t->kategoria;  	  	
        $datum_od     = $t->datum_od_format();
        $datum_do     = $t->datum_do_format();		
        $vysledok     =	$t->vysledok;
        $pocet_timov  = $t->pocet_timov;
        $tim_Out      = $t->nazov_timu();
        $report       = $t->report;    
        $mesto        = $t->mesto;
        $stat         = $t->stat;
        $tim_Out      = $t->tim_Out;
        if($t->spirit == 1) $spirit = ' checked';
        else $spirit = '';                  
    
    }
  } else {
      //mazeme turnaj
      $t = new Turnaj($_GET['id']);
      if((!$t->ok()) || ($akcia != 'zmaz')) redirect('turnaje.php');
    
      $t->delete();     
      redirect('turnaje.php');       
  }
  
  include('inc/header.inc.php'); 
?>
          
    <?php if($akcia == 'novy'): ?>
      <h3>Pridaj nový turnaj</h3>
    <?php elseif($akcia == 'zmen'): ?>
      <h3>Zmeň turnaj</h3>
    <?php endif; ?>
		    
      <?php if($akcia == 'novy'): ?>
        <form action="admin-turnaj.php?akcia=novy" method="post">
      <?php elseif($akcia == 'zmen'): ?>
        <form action="admin-turnaj.php?akcia=zmen&id=<?php echo $id; ?>" method="post">
      <?php endif; ?>
      
          <?php echo $Error; ?>    	
          <table>  
            <tr><td><label for="turnaj">Turnaj:*</label></td><td><input type="text" name="turnaj" id="turnaj" value="<?php echo $turnaj; ?>" class="text"></td></tr>
            <tr><td><label for="kategoria">Kategória:</label></td><td>
              <select name="kategoria" id="kategoria">
              <?php echo get_options("kategorie", "id", "kategoria", $kategoria); ?>
              </select>
            </td></tr>
            <tr><td><label for="mesto">Mesto:*</label></td><td><input type="text" name="mesto" id="mesto" value="<?php echo $mesto; ?>" class="text"></td></tr>
            <tr><td><label for="stat">Štát:*</label></td><td><input type="text" name="stat" id="stat" value="<?php echo $stat; ?>" class="text"></td></tr>
            <tr><td><label for="datum_od">Dátum od:*</label></td><td><input type="text" name="datum_od" id="datum_od" value="<?php echo $datum_od; ?>" class="text"></td></tr>
            <tr><td><label for="datum_od">Dátum do:*</label></td><td><input type="text" name="datum_do" id="datum_do" value="<?php echo $datum_do; ?>" class="text"></td></tr>
            <tr><td><label for="vysledok">Výsledok:</label></td><td><input type="text" name="vysledok" id="vysledok" value="<?php echo $vysledok; ?>" class="text"></td></tr>
            <tr><td><label for="pocet_timov">Počet tímov:</label></td><td><input type="text" name="pocet_timov" id="pocet_timov" value="<?php echo $pocet_timov; ?>" class="text"></td></tr>
            <tr><td><label for="spirit">Získali sme Spirit:</label></td><td><input type="checkbox" name="spirit" id="spirit" value="1"<?php echo $spirit;?>></td></tr>
            <tr><td><label for="tim_Out">Tím Outsiterz:</label></td><td>
              <select name="tim_Out" id="tim_Out">
                <option value=""<?php if($tim_Out == 0) echo ' selected="selected"'; ?>></option>
                <?php echo get_options("timy", "id", "tim", $tim_Out); ?>              
              </select>
            </td></tr>
            <tr><td><label for="report">Link na report z turnaja:</label></td><td><input type="text" name="report" id="report" value="<?php echo $report; ?>" class="text"></td></tr>
            <?php 
              if($akcia == 'novy') $value = 'Pridaj turnaj';
              elseif($akcia == 'zmen') $value = 'Zmeň turnaj';
            ?>
					  <tr><td colspan="2" align="right"><input type="submit" name="btnPridajTurnaj" value="<?php echo $value; ?>" id="btnPridajTurnaj"></td></tr>
          </table>           			 
				</form>                     		

<?php include('inc/footer.inc.php'); ?>