<?php
# Rdio Playlist Editor
#
# Copyright (c) 2015 Ying Zhang
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#

define('RDIO_CLIENT_ID', '');
define('RDIO_CLIENT_SECRET', '');
define('RDIO_REDIRECT_URI', '');
define("WWWROOT", me());

require_once 'lib/rdiolib-php/rdiolib.php';
$rdio = new RdioLib(RDIO_CLIENT_ID, RDIO_CLIENT_SECRET, RDIO_REDIRECT_URI);
session_start();

if (isset($_GET['logout']))
{
	logout();
}
elseif (isset($_GET['login']))
{
	$_SESSION["rpleditor_splash_displayed"] = true;
}

if (!isset($_SESSION["rpleditor_splash_displayed"]))
{
	require('templates/header.php');
	require('templates/login.php');
	require('templates/footer.php');
	die;
}

$auth = $rdio->authenticate();
if ($auth == RdioLib::AUTH_SUCCESS_INITIAL || $auth != RdioLib::AUTH_SUCCESS)
{
	header('Location: '.WWWROOT);
	die;
}

if (empty($_SESSION["user"]))
{
	$_SESSION["user"] = $rdio->currentUser()->result;
}

if (empty($_SESSION["user"]))
{
	logout();
}

if (isset($_GET['save']))
{
	$ret = $rdio->setPlaylistOrder([
		'playlist'=>$_POST["playlist"],
		'tracks'=>implode(",", $_POST['keys'])
		]);
	die(json_encode(array('status'=>'ok')));
}
elseif (isset($_GET['delete']))
{
	$ret = $rdio->removeFromPlaylist([
		'playlist'=>$_POST["playlist"],
		'index'=>$_POST['index'],
		'count'=>1,
		'tracks'=>$_POST['track']
		]);
	die(json_encode(array('status'=>'ok')));
}
elseif (isset($_GET['playlist']))
{
	$tmp = $rdio->get(['keys'=>$_GET['playlist'], 'extras'=>'trackKeys'])->result->$_GET['playlist'];
	$trackarr = $tmp->trackKeys;
	$tracks = $rdio->get(['keys'=>implode(",", $trackarr)])->result;
	require('templates/playlist.php');
}
else
{
	$myPlaylists = $rdio->getPlaylists()->result->owned;
	require('templates/header.php');
	require('templates/home.php');
	require('templates/footer.php');
}

/*
 * Functions
 */

function asset($file)
{
	return WWWROOT."/assets/{$file}";
}

function me()
{
	if (@$_SERVER["HTTPS"] == "on")
	{
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

function logout()
{
	session_destroy();
	header('Location: '.WWWROOT);
	die;
}
