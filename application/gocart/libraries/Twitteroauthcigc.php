<?php

/*
 * admin@hypersocialmobile.com adapted this for Code Igniter & Gocart
 *
 * Original class by:
 *
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * The first PHP Library to support OAuth for Twitter's REST API.
 */

/* This class adapts the original class for code igniter*/
require_once('twitteroauth/twitteroauth.php');

/**
 * Twitter OAuth class adapted for CI loader
 */
class Twitteroauthcigc extends TwitterOAuth {

  /**
   * construct TwitterOAuth object
   */
  function __construct($data) {
      $consumer_key = $data['consumer_key'];
      $consumer_secret  = $data['consumer_secret'];
      $oauth_token =  ($data['oauth_token']) ? $data['oauth_token'] : NULL ;
      $oauth_token_secret = ($data['oauth_token_secret']) ? $data['oauth_token_secret'] : NULL ;

    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }

}
