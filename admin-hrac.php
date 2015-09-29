<?php
  //TODO: ochrana proti nahodnemu zmazaniu
  //TODO: vymazat zo zostav zmienky o hracovi)
  //TODO: domovsky klub by mal byt combobox...
  //validne linky na profil a fotku?
  
	require 'includes/master.inc.php';
  $nav = 'admin-hrac';
  
	$Auth->requireUser('login');
  
  if((!isset($_GET['akcia'])) || ($_GET['akcia'] == '')) $akcia = 'novy';
  else $akcia = $_GET['akcia'];

  //novy hrac
  if(($akcia == 'novy') || ($akcia == 'zmen')) {
  	if(isset($_POST['btnPridajHraca']))
  	{
  		$Error->blank($_POST['prezyvka'], 'prezyvka', 'Prezývka');
  				
  		if($Error->ok())
  		{
  			if($akcia == 'novy') $h = new Hrac();
        else $h = new Hrac($_GET['id']);
                
  			$h->prezyvka     = $_POST['prezyvka'];
  			$h->meno         = $_POST['meno'];
  			$h->priezvisko   = $_POST['priezvisko'];
  			$h->profil       = $_POST['profil'];
  			$h->domaci_tim   = $_POST['domaci_tim'];
  			$h->poznamka     = $_POST['poznamka'];
        $h->foto         = $_POST['foto'];
        
        if($h->domaci_tim != '') $h->externe = 1;
        else $h->externe = 0;			
  			
        if($akcia == 'novy') $h->insert();
        else $h->update();
  
        redirect('hrac.php?id='.$h->id);
  		}
  		else
  		{
  			$prezyvka     = $_POST['prezyvka'];
  			$meno         = $_POST['meno'];
  			$priezvisko   = $_POST['priezvisko'];
  			$profil       = $_POST['profil'];
  			$domaci_tim   = $_POST['domaci_tim'];
  			$poznamka     = $_POST['poznamka'];
  			$foto         = $_POST['foto'];        
  		}
  	}
  	elseif($akcia == 'novy')
  	{
  	    $prezyvka     = '';
  			$meno         = '';
  			$priezvisko   = '';
  			$profil       = 'http://www.outsiterz.org/?p=';
  			$domaci_tim   = '';
  			$poznamka     = '';
  			$foto        = 'http://';
  	}
  	else {
  	    //vytiahneme udaje o menenom hracovi
  	    $h = new Hrac($_GET['id']);
  	    $id           = $h->id; 
  	    $prezyvka     = $h->prezyvka;
  			$meno         = $h->meno;
  			$priezvisko   = $h->priezvisko;
  			$profil       = $h->profil;
  			$domaci_tim   = $h->domaci_tim;
  			$poznamka     = $h->poznamka;
  			$foto         = $h->foto;
    
    }
  } else {
      //mazeme hraca
      $h = new Hrac($_GET['id']);
      if((!$h->ok()) || ($akcia != 'zmaz')) redirect('hraci.php');
    
      $h->delete();     
      redirect('hraci.php');       
  }
  
  include('inc/header.inc.php'); 
?>
    
  <?php if($akcia == 'novy'): ?>
    <h3>Pridaj nového hráča</h3>
  <?php elseif($akcia == 'zmen'): ?>
    <h3>Zmeň hráča</h3>
  <?php endif; ?>
	
	    
    <?php if($akcia == 'novy'): ?>
      <form action="admin-hrac.php?akcia=novy" method="post">
    <?php elseif($akcia == 'zmen'): ?>
      <form action="admin-hrac.php?akcia=zmen&id=<?php echo $id; ?>" method="post">
    <?php endif; ?>
    
        <?php echo $Error; ?>    	
        <table>  
          <tr><td><label for="prezyvka">Prezývka:*</label></td><td><input type="text" name="prezyvka" id="prezyvka" value="<?php echo $prezyvka; ?>" class="text"></td></tr>
				  <tr><td><label for="meno">Meno:</label></td><td><input type="text" name="meno" id="meno" value="<?php echo $meno; ?>" class="text"></td></tr>
				  <tr><td><label for="priezvisko">Priezvisko:</label></td><td><input type="text" name="priezvisko" id="priezvisko" value="<?php echo $priezvisko; ?>" class="text"></td></tr>
          <tr><td><label for="profil">Profil na stránke:</label></td><td><input type="text" name="profil" id="profil" value="<?php echo $profil; ?>" class="text"></td></tr>          
          <tr><td><label for="domaci_tim">Domáci klub (ak je iný ako Outsiterz):</label></td><td><input type="text" name="domaci_tim" id="domaci_tim" value="<?php echo $domaci_tim; ?>" class="text"></td></tr>
          <tr><td><label for="poznamka">Ľubovoľná poznámka k hráčovi:</label></td><td><input type="text" name="poznamka" id="poznamka" value="<?php echo $poznamka; ?>" class="text"></td></tr>
          <tr><td><label for="foto">Link na fotku hráča:</label></td><td><input type="text" name="foto" id="foto" value="<?php echo $foto; ?>" class="text"></td></tr>
          <?php 
            if($akcia == 'novy') $value = 'Pridaj hráča';
            elseif($akcia == 'zmen') $value = 'Zmeň hráča';
          ?>
				  <tr><td colspan="2" align="right"><input type="submit" name="btnPridajHraca" value="<?php echo $value; ?>" id="btnPridajHraca"></td></tr>
        </table>           			 
			</form>  			

<?php include('inc/footer.inc.php'); ?>
