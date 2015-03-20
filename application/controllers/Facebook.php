<?php 


		# Faire lien vers sdk FB
		require_once(APPPATH . 'libraries/' . 'fb-sdk/autoload.php');

		# On inclut ce qui nous est utile
		use Facebook\FacebookSession;
		use Facebook\FacebookRedirectLoginHelper;
		use Facebook\FacebookRequest;




class Facebook extends CI_Controller{

	# Ici les params que l'ont va transmettre à l'API FB
	private $appId = "1546926692223129";
	private $appSecret = "a6adb59f995f4a6495b8317a9147f002";
	private $redirectUrl = "http://localhost:8081/Ecole/Facebook/sondage/index.php/facebook/connect";
	private $next = "http://localhost:8081/Ecole/Facebook/sondage/index.php/facebook/index";

	private $permissions = array('email'); # Elements que l'on veut recuperer


	public function __construct()
	{



		parent::__construct();

		# On charge le modele de données 'user_model' qui interagi avec la BDD facebook.sondage
		$this->load->model('user_model');

		# On demmare la session à chaque utilisation de la classe
		session_start();
		
	}

	public function connect()
	{

		try {
				# Execution de la requete vers l'API FB avec les membres de la classe
				FacebookSession::setDefaultApplication($this->appId, $this->appSecret);
				$helper = new FacebookRedirectLoginHelper($this->redirectUrl);

				# Creation de la session dans laquelle on met les data recoltees
				$session = $helper->getSessionFromRedirect();
			}
		catch(FacebookRequestException $ex)
		{
				echo "La récupération de la session a échoué";
		}
		catch(\Exception $ex)
		{
				echo "Erreur inconnue";
		}
		


		if ($session) # Ici la session est ouverte #Donc des data recoltees
		
		{
			# On va recuperer les données FB envoyées
			$user_profile = (new \Facebook\FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(Facebook\GraphUser::className());
			//var_dump($user_profile);

			# Creation d'un objet utilisateur
			$user = array();
			$user['id_fb'] = $user_profile->getProperty('id');
			$user['nom'] = (string)$user_profile->getName();
			$user['mail'] = $user_profile->getProperty('email');
			$user['sexe'] = $user_profile->getProperty('gender');

			# On regarde si le compte existe deja ds notre bdd
			$ourUser = $this->user_model->get_user($user['id_fb']);

			#echo count($ourUser);

			if (count($ourUser) == 0) # Si l'utilisateur n'existe pas, on créer un utilisateur
			{
				echo "l'utilisateur n'existe pas !";
				$this->user_model->set_user($user);
			}

			$_SESSION['user'] = $user;

			$data['text'] = 'Vous etes bien connecte à votre session FB';


			# On creer le bouton de deconnexion
			$logouturl = $helper->getLogoutUrl($session, $this->next);
			$data['link'] = '<a href="'.$logouturl.'">Quitter</a>';

		}
		else
		{
			# On creer le bouton de connexion
			$loginUrl = $helper->getLoginUrl($this->permissions);
			$data['link'] = '<a href="'.$loginUrl.'">Connexion</a>';
		}

		# Envoie des données vers la vue
		$this->load->view('templates/header', $data);
        $this->load->view('facebook/connect', $data);
        $this->load->view('templates/footer');
		
	}

	public function index()
	{

		

		# Envoie des données vers la vue
		$this->load->view('templates/header', $data);
        $this->load->view('facebook/index', $data);
        $this->load->view('templates/footer');
	}


}



 ?>