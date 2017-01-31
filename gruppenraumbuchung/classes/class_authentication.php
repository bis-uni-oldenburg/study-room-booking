<?php
// Class Authentication

class Authentication
{
	
	// Members
	
	protected $auth_user;
	protected $auth_password;
	protected $auth_method;
	protected $access;
	protected $table;
	protected $tbl_apps;

	// Constructor
	
	function __construct($auth_method)
	{
		$this->auth_method=$auth_method;
		
		switch($auth_method)
		{
			case "cas":
				// initialize phpCAS
				// Import phpCAS lib if installed
				include_once('CAS/CAS.php');
				phpCAS::client(CAS_VERSION_2_0, 'cas.elearning.uni-oldenburg.de', 443, '/cas');
				
				phpCAS::forceAuthentication();
				$this->auth_user=phpCAS::getUser();
				$_SESSION["ub_user"]=$this->auth_user;
				
				// logout if desired
				if(isset($_REQUEST['logout'])) {
					$_SESSION["ub_user"]=0;
					phpCAS::logout();
				}
				break;
				
			case "custom_login":
				$login=1;
				if(isset($_POST["login_id"]))
				{
					$login_id=$_POST["login_id"];
					$password=$_POST["password"];
					
					if($this->accessGranted($login_id, $password))
					{
						$this->auth_user=$login_id;
						$_SESSION["ub_user"]=$this->auth_user;
						$login=0;
					}
					else $login=2;
				}
				else if(isset($_REQUEST['logout'])) 
				{
					$_SESSION["ub_user"]=0;
					$login=0;
				}

				if($login)
				{
					$template=new Template();
					$login_html=$template->getTemplate("login");
					
					if($login == 2) $meldung="Login fehlgeschlagen. Bitte �berpr�fen Sie Ihre Zugangsdaten.";
					else $meldung="";
					
					$login_html=$template->tplReplace($login_html, array(
	                	"header"=>$template->getTemplate("header"),
	                	"footer"=>$template->getTemplate("footer"),
						"meldung"=>$meldung,
						"css"=>"",
						"javascript"=>""
					));
					
					echo $login_html;
					exit;
				}

				break;	
		}

	}
	
	
	// Methods
	
	public function getUser()
	{
		return $this->auth_user;
	}
	
	protected function accessGranted($login_id, $password)
	{
		// Nur zu Testzwecken!
		if(($login_id == "12345" or $login_id == "12346") and $password == "test") return true;
		else return false;

		/* Hier muss die lokale Authentifizierungsdatenbank (z. B. LDAP)
		 * abgefragt werden. 
		 * Gibt es das durch Login-ID und Passwort beschriebene Konto, wird
		 * true zur�ckgegeben, ansonsten false.
		 * */
		
	}
	
	public static function isValidLoginId($login_id)
	{
		/*
		 * Lokale Regeln für valide Login_IDs
		* hier: abcd1234
		*/
		$regex="!^[a-z]{4}[0-9]{4}$!";
	
		if(preg_match($regex, $login_id))
		{
			return true;
		}
		else return false;
	}
	
	public static function normalizeLoginId($login_id)
	{
		/*
		 * Lokale Regeln für Normalisierung von Login-IDs
		* hier: to lower case
		*/
		//return $login_id;
	
		return strtolower($login_id);
	
	}
	
	
}
?>