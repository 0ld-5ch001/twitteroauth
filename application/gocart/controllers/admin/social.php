<?php
class Social extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		remove_ssl();

		$this->auth->check_access('Admin', true);
        $this->config->load('social');
        $this->load->helper('url');
	}
		
	function index()
	{
        session_start();
	   /* If access tokens are not available redirect to connect page. */
        if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
          redirect('admin/social/clearsessions');
        }

        /* Get user access tokens out of the session. */
        $access_token = $_SESSION['access_token'];

        /* Create a TwitterOauth object with consumer/user tokens. */
        $oauth_data = array('consumer_key' => $this->config->item('consumer_key'), 'consumer_secret' => $this->config->item('consumer_secret'), 'oauth_token' => $access_token['oauth_token'], 'oauth_token_secret' => $access_token['oauth_token_secret']);
        $this->load->library('twitteroauthcigc', $oauth_data);

        /* If method is set change API call made. Test is called by default. */
        $data['content'] = $this->twitteroauthcigc->get('account/verify_credentials');

        $this->load->view($this->config->item('admin_folder').'/social', $data);
	}

    function clearsessions()
    {
        /* Load and clear sessions */
        session_start();
        session_destroy();

        /* Redirect to page with the connect to Twitter option. */
        redirect('admin/social/connect');

    }

    function connect()
    {
        /**
         * Exit with an error message if the CONSUMER_KEY or CONSUMER_SECRET is not defined.
         */
        if ($this->config->item('consumer_key') === '' || $this->config->item('consumer_secret') === '' || $this->config->item('consumer_key') === 'CONSUMER_KEY_HERE' || $this->config->item('consumer_secret') === 'CONSUMER_SECRET_HERE') {
            echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://dev.twitter.com/apps">dev.twitter.com/apps</a>';
            exit;
        }

        /* Build an image link to start the redirect process. */
        $data['content'] = '<a href="./redirect"><img src="' . base_url('assets/img/lighter.png') . '" alt="Sign in with Twitter"/></a>';
        $this->load->view($this->config->item('admin_folder').'/social', $data);
    }


    function redirect()
    {
        /* Start session and load library. */
        session_start();

        /* Build TwitterOAuth object with client credentials. */
        $oauth_data = array('consumer_key' => $this->config->item('consumer_key'), 'consumer_secret' => $this->config->item('consumer_secret'));
        $this->load->library('twitteroauthcigc', $oauth_data);

        /* Get temporary credentials. */
        $request_token = $this->twitteroauthcigc->getRequestToken($this->config->item('oauth_callback'));

        /* Save temporary credentials to session. */
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        /* If last connection failed don't display authorization link. */
        switch ($this->twitteroauthcigc->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $this->twitteroauthcigc->getAuthorizeURL($token);
                header('Location: ' . $url);
                break;
            default:
                /* Show notification if something went wrong. */
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    }


    function callback()
    {
        session_start();

        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
            $_SESSION['oauth_status'] = 'oldtoken';
            redirect('admin/social/clearsessions');
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $oauth_data = array('consumer_key' => $this->config->item('consumer_key'), 'consumer_secret' => $this->config->item('consumer_secret'), 'oauth_token' => $_SESSION['oauth_token'], 'oauth_token_secret' => $_SESSION['oauth_token_secret']);
        $this->load->library('twitteroauthcigc', $oauth_data);

        /* Request access tokens from twitter */
        $access_token = $this->twitteroauthcigc->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);


        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $this->twitteroauthcigc->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            $_SESSION['status'] = 'verified';
            redirect('admin/social/index');
        } else {
            /* Save HTTP status for error dialog on connnect page.*/
            redirect('admin/social/clearsessions');
             }


    }
}