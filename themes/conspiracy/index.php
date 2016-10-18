<?php

function getSlug( $page ) {
   $page = strip_tags( $page );
   preg_match_all( "/([a-z0-9A-Z-_]+)/", $page, $matches );
   $matches = array_map( "ucfirst", $matches[0] );
   $slug = implode( "-", $matches );
   return $slug;
}
   $page = $_REQUEST['page'];
   if(!$page) $page = "Home";

   $contentfile = $page = getSlug( $page );
   $content[0] = @file_get_contents("files/$contentfile.txt");
   if(!$content[0]) $content[0] = "<h1>$page</h1><br><br><p class='side'>This element is unique on every page</p>Click here to edit the page<p class='round'><b>Rounded Edges</b><br><br>Typewriter font</p>";
   
   $title = @file_get_contents('files/title.txt');
   if(!$title) $title = "Your title here";
   $slogan = @file_get_contents('files/slogan.txt');
   if(!$slogan) $slogan = "- Your slogan over here!";

   $menu = @file_get_contents('files/menu.txt');
   if(!$menu) $menu = "Home";
   
   $description = @file_get_contents('files/description.txt');
   if(!$description) $description = "Enter your website description over here!";
   $keywords = @file_get_contents('files/keywords.txt');
   if(!$keywords) $keywords = "Enter, your, keywords, for, your, website, over, here";

   $copyright = @file_get_contents('files/copyright.txt');
   if(!$copyright) $copyright = "Your website (c) 2011";
   $mess = 'Powered by <a href="http://krneky.com/en/wondercms">WonderCMS</a>';

//config section
	$hostname = $_SERVER['PHP_SELF'];
	$hostname = str_replace('index.php', '', $hostname);
	$hostname = str_replace($page, '', $hostname);

   $theme = $_REQUEST['theme'];
   if( !file_exists("$theme.php") ) $theme = "default";

   $cookie      = 'wondercms';	// cookie ame
   $expirytime  = time()+86400; // expire time
		
   if(isset($_REQUEST['logout']))
   {
	   setcookie($cookie,'',time() - 84000); // remove cookie/
	   header('Location: ./');
	   exit;
   }
   $password = @file_get_contents("files/password");
   if(!$password)
   {
	   savePassword("admin");
   }
   if($_COOKIE[$cookie])
   {
	   $lstatus = "<a href='$hostname?logout'>Logout</a>";
   }
   else	$lstatus = "<a href='$hostname?login'>Login</a>";
   
   if(isset($_REQUEST['login']))
   {
	   getLoginForm();
   }
   require("$theme.php");

// Functions
   function editTags()
   {
	   global $cookie;
	   if(!$_COOKIE[$cookie]) return;

	   echo  "<script type='text/javascript' src='./js/editInplace.js'></script>";
   }

   function displayMainContent()
   {
	   global $cookie, $content, $page;
	   
	   if($_COOKIE[$cookie])
	   {
		   echo "<div class='title'><div id='change'><span id='$page' class='editText'>$content[0]</span></div></div>";
	   }
	   else
	   {
		   echo $content[0];
	   }
   }

// display section content
   function displaySectionContent($cnum)
   {
	   global $cookie, $content;
	   
	   if($_COOKIE[$cookie])
	   {
		   echo "<div id='change'><span id='$cnum' class='editText'>$content[$cnum]</span></div>";
	   }
           else	echo $content[$cnum];
   }

   function displayMenu($stags,$etags)
   {
	   global $menu;
	   
	   $mlist = explode("<br />",$menu);
	   $num = count($mlist);

	   for($ix=0;$ix<$num;$ix++)
	   {
		   $page = trim($mlist[$ix]);
		   if(!$page) continue;
		   echo "$stags href='$page'>$page $etags \n";
	   }
   }
   
   function getLoginForm()
   {
	   global $content, $msg;
	   
	   $msg = "";
	   
	   if (isset($_POST['sub'])) loginSubmitted();
	   $content[0] = "
<center>
<form action='' method='POST'>
<h2>Password</h2>
<input type='password' name='password' /><br />
<input type='submit' name='login' value='Login'>
<h2>$msg</h2><br />

<script src='js/editInplace.js'></script> 
<div class='all'>
<a href='javascript:showhide()'>Click to change your password</a> <br /><br />
<div id=hide style='display: none;'>
Type your <b>old</b> password above, and your new one in the field below.
<h2>New Password</h2>
<input type='password' name='new' /><br />
<input type='submit' name='login' value='Change'>
<input type='hidden' name='sub' value='sub'>
</div></div>
</form></center>";
   }
   
   function loginSubmitted()
   {
	   global $cookie, $password, $msg, $expirytime, $submitted_pass;
	   
	   $submitted_pass = md5($_POST['password']);

	   if ($submitted_pass<>$password)
	   {
		   $msg = "<b class='wrong'>Wrong Password</b>";
		   return;
	   }
	   if($_POST['new'])
	   {
		   savePassword($_POST['new']);
		   $msg = "Password changed!<br /><br />Please login again.";
		   return;
	   }
	   setcookie($cookie,$password,$expirytime);
	   header('Location: ./');
	   exit;
   }
   
   function savePassword($password)
   {
	   $password = md5($password);
	   $file = @fopen("files/password", "w");
	   if(!$file)
	   {
		   echo "<h2 style='color:red'>*Error* - unable to access password</h2><h3>But don't panic!</h3>".
				   "Set the correct read/write permissions to the password file. <br />
				    Find the password file in the /files/ directory and CHMOD it to 640.<br /><br />
				    If this doesn't work, use <a href='http://krneky.com/forum'>this forum</a>.";
		   exit;
	   }
	   fwrite($file, $password);
	   fclose($file);
   }

   function extraSettings()
   {
	   global $description, $keywords, $title, $slogan, $copyright, $menu;
	   echo "<div class='settings'>
<h3>Extra Settings</h3>
<a href='javascript:showhide()'>Cick here to open/close settings</a> <br /><br />
<div id=hide style='display: none;'>
<ul class='linkss'>
<b>Add Page: (in a new row) and <a href='javascript:location.reload(true);'>click here to refresh the page</a>.</b><br />
<div id='change'><span id='menu' class='editText'>$menu</span></div>
</ul>
<ul class='linkss'>
<b>Title:</b><br />
<div id='change'><span id='title' class='editText'>$title</span></div>
</ul>
<ul class='linkss'>
<b>Slogan:</b><br />
<div id='change'><span id='slogan' class='editText'>$slogan</span></div>
</ul>
<ul class='linkss'> 
<b>Meta Description:</b><br />
<div id='change'><span id='description' class='editText'>$description</span></div>
</ul>
<ul class='linkss'>
<b>Meta Keywords:</b><br />
<div id='change'><span id='keywords' class='editText'>$keywords</span></div>
</ul>
<ul class='linkss'>
<b>Copyright:</b><br />
<div id='change'><span id='copyright' class='editText'>$copyright</span></div>
</ul>
<br />
If you want to use a copyright sign: &copy; - Simply copy this into your footer: &#38;&#99;&#111;&#112;&#121;&#59;
</div></div>";
   }
?>