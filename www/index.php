<?php
/* Rdio Playlist Editor
 * by Ying Zhang
 * https://github.com/ying17/rpleditor
 *
 * TODO: refactor to use slimframework.com
 */

define('RDIO_CONSUMER_KEY', '');        // put key here
define('RDIO_CONSUMER_SECRET', '');     // put secret here

define("WWWROOT", me());
define("HOMEDIR", dirname(__FILE__));

set_include_path(HOMEDIR.PATH_SEPARATOR.HOMEDIR.PATH_SEPARATOR."lib");

/* Rdio includes, see
 * http://developer.rdio.com/docs/read/rest/rdiosimple
 */
require_once 'rdio/rdio.php';

$page = array('title'=>"Playlist Editor for Rdio");
$rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET));
$loggedin = false;

session_start();

if (isset($_GET['logout'])) {
	session_destroy();
	require('templates/header.php');
	require('templates/login.php');
	require('templates/footer.php');
	die;
}

if (@$_SESSION['oauth_token'] && @$_SESSION['oauth_token_secret'])
{
	$rdio->token = array($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	if (@$_GET['oauth_verifier'])
	{
		$rdio->complete_authentication($_GET['oauth_verifier']);
		$_SESSION['oauth_token'] = $rdio->token[0];
		$_SESSION['oauth_token_secret'] = $rdio->token[1];

		$currentUser = $rdio->call('currentUser');
		header('Location: '.WWWROOT);
	}

	$loggedin = true;
	$currentUser = $rdio->call('currentUser');

	if (!$currentUser)
	{
		session_destroy();
		header('Location: '.WWWROOT);
		die;
	}

	if (isset($_GET['save']))
	{
		$ret = $rdio->call('setPlaylistOrder', array(
			'playlist'=>$_POST["playlist"],
			'tracks'=>implode(",", $_POST['keys'])
			));
		die(json_encode(array('status'=>'ok')));
	}
	elseif (isset($_GET['delete']))
	{
		$ret = $rdio->call('removeFromPlaylist', array(
			'playlist'=>$_POST["playlist"],
			'index'=>$_POST['index'],
			'count'=>1,
			'tracks'=>$_POST['track']
			));
		die(json_encode(array('status'=>'ok')));
	}
	elseif (@$_GET['playlist'])
	{
		$tmp = $rdio->call('get', array('keys'=>$_GET['playlist'], 'extras'=>'trackKeys'))->result->$_GET['playlist'];
		$trackarr = $tmp->trackKeys;
		$tracks = $rdio->call('get', array('keys'=>implode(",", $trackarr)))->result;
		require('templates/playlist.php');
	}
	else
	{
		$myPlaylists = $rdio->call('getPlaylists')->result->owned;
		require('templates/header.php');
		require('templates/home.php');
		require('templates/footer.php');
	}
}
else
{
	if (isset($_GET["login"]))
	{
		$authorize_url = $rdio->begin_authentication(WWWROOT);
		$_SESSION['oauth_token'] = $rdio->token[0];
		$_SESSION['oauth_token_secret'] = $rdio->token[1];
		header('Location: '.$authorize_url);
		die;
	}
	else
	{
		require('templates/header.php');
		require('templates/login.php');
		require('templates/footer.php');
		die;
	}
}

/* Functions
 */

function asset($file)
{
	return WWWROOT."/assets/{$file}";
}

function me()
{
	if (@$_SERVER["HTTPS"] == "on") {
		$me = ($_SERVER["SERVER_PORT"] != 443)
			? "https://{$_SERVER["SERVER_PORT"]}"
			: "https://";
	}
	else
	{
		$me = ($_SERVER["SERVER_PORT"] != 80)
			? "http://{$_SERVER["SERVER_PORT"]}"
			: "http://";
	}

	$me .= $_SERVER["SERVER_NAME"].str_replace('/index.php', '', $_SERVER["SCRIPT_NAME"]);
	return $me;
}
