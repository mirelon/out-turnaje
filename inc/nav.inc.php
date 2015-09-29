<?php

  if(isset($_GET["od"])) $get_od = $_GET["od"];
  else $get_od = null;
  
  if(isset($_GET["do"])) $get_do = $_GET["do"];
  else $get_do = null;
  
?>
  
  </div>
  <div id="sidebar">
		<ul class="avmenu">
		  <li>
      <?php
        echo '<a ';
        if(($nav == 'index') || ($nav == "")) echo 'class="current ';
        echo 'href="index.php';
        if(!is_null($get_od)) echo '?od='.$get_od.'&amp;';
        if(!is_null($get_do)) echo 'do='.$get_do;
        echo '">Hlavná stránka</a>';
      ?>
      </li>
      
      <li>
      <?php
        echo '<a ';
        if($nav == 'turnaje') echo 'class="current ';
        echo 'href="turnaje.php';
        if(!is_null($get_od)) echo '?od='.$get_od.'&amp;';
        if(!is_null($get_do)) echo 'do='.$get_do;
        echo '">Turnaje</a>';
      ?>
      </li>
      
      <li>
      <?php
        echo '<a ';
        if($nav == 'hraci') echo 'class="current ';
        echo 'href="hraci.php';
        if(!is_null($get_od)) echo '?od='.$get_od.'&amp;';
        if(!is_null($get_do)) echo 'do='.$get_do;
        echo '">Hráči</a>';
      ?>
      </li>

      <li><a href="http://www.outsiterz.org">outsiterz.org</a></li>		
		</ul>	    
    
    <?php if($Auth->loggedIn()): ?>
    <h3>Prihlásený</h3>
		<p>		  
      <?php echo $Auth->user->username; ?>
      <br />
      <a href="nastavenia.php">Nastavenia</a> | <a href="logout.php">Odhlásiť</a>
      <br />
      Administrácia<br />
      <a href="admin-hrac.php?akcia=novy">Nový hráč</a> |
      <a href="admin-turnaj.php?akcia=novy">Nový turnaj</a><br/>
      <a href="admin-zapas.php?akcia=novy">Nový zápas</a> |                     
    </p>
    <?php endif; ?>
    			
	</div>

