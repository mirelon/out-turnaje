<?php   
	require 'includes/master.inc.php';
	$nav = 'nastavenia'; 
	$Auth->requireUser('index');
	$u = $Auth->user; 
  		
  //zmenime heslo		
	if(isset($_POST['btnZmenHeslo']))
	{
		$Error->blank($_POST['stare'], 'stare', 'Staré heslo');    
		if(($_POST["stare"] != "") && ($Auth->hashedPassword($_POST["stare"]) != $u->password)) 
      $Error->add('stare', 'Nesprávne zadané staré heslo.');
		$Error->blank($_POST['nove'], 'nove', 'Nové heslo');
		$Error->blank($_POST['nove2'], 'nove2', 'Nové heslo ešte raz');
    $Error->passwords($_POST['nove'], $_POST['nove2'], 'nove');
	  
		if($Error->ok()) {
      $Auth->changeCurrentPassword($_POST["nove"]);
    } 
  }    
  
  include('inc/header.inc.php'); 
?>  
				
    <h3>Zmena hesla</h3>      
    <?php  
      if(($Error->ok()) && (isset($_POST['btnZmenHeslo']))):
    ?>
    <p class='alert notice'>Heslo bolo úspešne zmenené.</p>
    <?php  
      elseif(isset($_POST['btnZmenHeslo'])):
       echo $Error;
      endif; 
    ?>		
	    <form action="nastavenia.php" method="post">		 
        <table>   
			    <tr><td><label for="stare">Staré heslo:*</label></td><td><input type="password" name="stare" id="stare" value="" class="text"></td></tr>
			    <tr><td><label for="nove">Nové heslo:*</label></td><td><input type="password" name="nove" id="nove" value="" class="text"></td></tr>
 				  <tr><td><label for="nove2">Nové heslo ešte raz:*</label></td><td><input type="password" name="nove2" id="nove2" value="" class="text"></td></tr>
				  <tr><td colspan="2" align="right"><input type="submit" name="btnZmenHeslo" value="Zmeň heslo" id="btnZmenHeslo"></td></tr>
        </table>
			</form>  				

<?php include('inc/footer.inc.php'); ?>
