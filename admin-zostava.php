<?php
//link v page title smeruje akoze nikam, lebo zostava bez setnuteho id turnaja je dost naprd
  
	require 'includes/master.inc.php';
  $nav = 'admin-zostava';
  
	$Auth->requireUser('login');
  
  if((!isset($_GET['akcia'])) || ($_GET['akcia'] == '')) $akcia = '';
  else $akcia = $_GET['akcia'];
  
  $id    = $_GET['id'];
  
  //ak nie je setnute id, presmerujeme na stranku turnajov 
  if(!isset($id)) redirect('turnaje.php');
  else {
    $t = new Turnaj($id);
    $turnaj  = $t->turnaj;
    $datum   = $t->datum_format();
    $link    = $t->link($turnaj.' ('.$datum.')');
    $zostava = $t->zostava();
  }    

  //pridat hraca do zostavy
  if($akcia == 'pridaj') {
  	if(isset($_POST['btnPridajHraca']))
  	{  	
      $hrac = $_POST['hrac'];	  				
  		if($Error->ok())
  		{ 		  	  		 
        $db = Database::getDatabase();  
        //najprv selectneme, ci tam uz nie je tato dvojica.        
        if(in_array($hrac, $zostava)) $Error->add('hrac', 'Hráč už je v zostave tohto turnaja.'); 
        else {
          //ak nie, tak ju pridam
          $query = "INSERT INTO zostavy (turnaj, hrac) VALUES ({$id}, {$hrac})";                
	        $db->query($query);
        }      		                        
  		}  		
  	}  	
  } elseif($akcia == "zmaz") {
      //zmazat hraca zo zostavy
      $hrac = $_GET["hrac"];
      $db = Database::getDatabase();
      $query = "DELETE FROM zostavy WHERE turnaj={$id} AND hrac={$hrac}";                
	    $db->query($query);
      $hrac = null;                          
  }  
  
  include('inc/header.inc.php');
    
?>

<script language="JavaScript">
  function potvrd() {
    return confirm("Naozaj chceš zmazať tohto hráča zo zostavy turnaja?")
  }
</script>
             
      <h3>Turnaj: <?php echo $link;?></h3>    
		          
      <form action="admin-zostava.php?id=<?php echo $id; ?>&akcia=pridaj" method="post">      
    
        <?php echo $Error; //TODO: ?>    	
        <table>  
          <tr><td><label for="hrac">Pridaj hráča do zostavy:*</td><td>        
            <select name="hrac" id="hrac">
            <?php echo get_options("hraci", "id", "prezyvka", $hrac, " order by prezyvka"); ?>
            </select>
          </td></tr>            
				  <tr><td colspan="2" align="right"><input type="submit" name="btnPridajHraca" value="Pridaj do zostavy" id="btnPridajHraca"></td></tr>
        </table>           			 
			</form>  
      
      <h3>Zostava</h3>
      
      <table class="lines">
    <thead>
      <tr>
			  <td>Prezývka</td>
				<td>Meno</td>
				<td>Priezvisko</td>
				<td>Domovský tím</td>				
				<td></td>
      </tr>
    </thead>
    <tbody>
		  <?php 
        $zostava = $t->zostava();
        
        foreach($zostava as $z) {
          $hrac = new Hrac($z);
      	  if($hrac->ok()) {
      	    echo $hrac->riadok_tabulky_admin_zostava($t->id);
      	  }
        }
      ?>			
    </tbody>
  </table> 
                    

<?php include('inc/footer.inc.php'); ?>
