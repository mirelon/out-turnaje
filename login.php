<?php    
    require 'includes/master.inc.php';
    $nav = 'login'; 

    // Kick out user if already logged in.
    if($Auth->loggedIn()) redirect('hraci.php');

    // Try to log in...
    if(!empty($_POST['username']))
    {
        $Auth->login($_POST['username'], $_POST['password']);
        if($Auth->loggedIn())
          redirect("hraci");
        else
          $Error->add('username', "Zadal si nesprávny login alebo heslo.");
    }

    // Clean the submitted username before redisplaying it.
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    
  include('inc/header.inc.php'); 
?>    
	  <h3>Administrátorský login</h3>
    
    <form action="login.php" method="post">
        <?php echo $Error; ?>
        <table>
          <tr><td><label for="username">Login:*</label></td><td><input type="text" name="username" value="<?PHP echo $username;?>" id="username" /></td></tr>
          <tr><td><label for="password">Heslo:*</label></td><td><input type="password" name="password" value="" id="password" /></td></tr>
          <tr><td colspan="2" align="right"><input type="submit" name="btnlogin" value="Login" id="btnlogin" /></td></tr>
        </table>
    </form>
    
<?php include('inc/footer.inc.php'); ?>