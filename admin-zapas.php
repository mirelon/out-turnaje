<?php
  
	require 'includes/master.inc.php';
  $nav = 'admin-zapas';
    
	$Auth->requireUser('login');
	  
  if((!isset($_GET['akcia'])) || ($_GET['akcia'] == '')) $akcia = 'novy';
  else $akcia = $_GET['akcia'];

  //novy zapas
  if(($akcia == 'novy') || ($akcia == 'zmen')) {
  	if(isset($_POST['btnPridajZapas']))
  	{
  		$Error->blank($_POST['super'], 'super', 'Súper'); 
  				
  		if($Error->ok())
  		{
  			if($akcia == 'novy') $z = new Zapas();
        else $z = new Zapas($_GET['id']);
                
  			$z->turnaj      = $_POST['turnaj'];
        $z->super       = $_POST['super'];
        $z->bodov_Out   = $_POST['bodov_Out'];
        $z->bodov_super = $_POST['bodov_super'];           	  		        
  			
        if($akcia == 'novy') {
          $z->insert();
          //presmerujeme na stranku zapasu
          redirect('zapas.php?id='.$z->id);
        }
        else {
          $z->update();
          //presmerujeme na stranku zapasu
          redirect('zapas.php?id='.$z->id);          
        }
        
  		}
  		else
  		{
  			$turnaj      = $_POST['turnaj'];  
        $super       = $_POST['super'];         		
        $bodov_Out   = $_POST['bodov_Out'];	  
        $bodov_super = $_POST['bodov_super'];         
  		}
  	}
  	elseif($akcia == 'novy')
  	{
  	    $turnaj      = '';
        $super       = '';  	  	
        $bodov_Out   = '';
        $bodov_super = '';	 
  	}
  	else {
  	    //vytiahneme udaje o menenom zapase
  	    $z = new Zapas($_GET['id']);
  	    $id          = $z->id; 
  	    $turnaj      = $z->turnaj;
        $super       = $z->super;  	  	
        $bodov_Out   = $z->bodov_Out;
        $bodov_super = $z->bodov_super;	                          
    }
  } else {
      //mazeme zapas
      $z = new Zapas($_GET['id']);
      if((!$z->ok()) || ($akcia != 'zmaz')) redirect('zapasy.php');
    
      $z->delete();     
      redirect('zapasy.php');       
  }
  
  include('inc/header.inc.php'); 
?>
          
    <?php if($akcia == 'novy'): ?>
      <h3>Pridaj nový zápas</h3>
    <?php elseif($akcia == 'zmen'): ?>
      <h3>Zmeň zápas</h3>
    <?php endif; ?>
		    
      <?php if($akcia == 'novy'): ?>
        <form action="admin-zapas?akcia=novy" method="post">
      <?php elseif($akcia == 'zmen'): ?>
        <form action="admin-zapas?akcia=zmen&id=<?php echo $id; ?>" method="post">
      <?php endif; ?>
      
          <?php echo $Error; ?>    	
          <table>  
            <tr><td><label for="turnaj">Turnaj:</label></td><td>
              <select name="turnaj" id="turnaj">
              <?php echo get_options("turnaje", "id", "turnaj", $kategoria); ?>
              </select>
            </td></tr>
            <tr><td><label for="super">Súper:*</label></td><td><input type="text" name="super" id="super" value="<?php echo $super; ?>" class="text"></td></tr>
            <tr><td><label for="bodov_Out">Bodov Outsiterz:</label></td><td><input type="text" name="bodov_Out" id="bodov_Out" value="<?php echo $bodov_Out; ?>" class="text"></td></tr>
            <tr><td><label for="bodov_super">Bodov súper:</label></td><td><input type="text" name="bodov_super" id="bodov_super" value="<?php echo $bodov_super; ?>" class="text"></td></tr>            
            <?php 
              if($akcia == 'novy') $value = 'Pridaj zápas';
              elseif($akcia == 'zmen') $value = 'Zmeň zápas';
            ?>
					  <tr><td colspan="2" align="right"><input type="submit" name="btnPridajZapas" value="<?php echo $value; ?>" id="btnPridajZapas"></td></tr>
          </table>           			 
				</form>                     		

<?php include('inc/footer.inc.php'); ?>
