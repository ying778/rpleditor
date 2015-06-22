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

require_once 'config.php';
require_once 'lib/rdiolib-php/rdiolib.php';

define("WWWROOT", me());

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

if (isset($_GET['playlist']))
{
	$playlist = $_GET["playlist"];
	$ret = $rdio->get([
		'keys'=>$playlist,
		'extras'=>'trackKeys'
		]);

	if ($ret->status == 'ok')
	{
		$trackarr =& $ret->result->$playlist->trackKeys;
		$tracks = $rdio->get(['keys'=>implode(",", $trackarr)])->result;
		require('templates/playlist.php');
	}
	else
	{
		die('Unable to retrieve playlist.');
	}
}
elseif (isset($_GET['playlist/save']))
{
	$delete =& $_POST['delete'];
	$tracks =& $_POST['tracks'];
	$delcount = 0;

	if (!empty($delete) && !empty($tracks))
	{
		foreach ($delete as $track)
		{
			$index = array_search($track, $tracks);
			if ($index !== false)
			{
				array_swap($tracks, $index, 0);
				$delcount++;
			}
		}
	}

	$ret = $rdio->setPlaylistOrder([
		'playlist'=>$_POST["playlist"],
		'tracks'=>implode(",", $tracks)
		]);

	if ($delcount && $ret->status == 'ok')
	{
		$ret = $rdio->removeFromPlaylist([
			'playlist'=>$_POST["playlist"],
			'tracks'=>implode(",", $tracks),
			'index'=>0,
			'count'=>$delcount,
			]);
	}

	if ($ret->status == 'ok') {
		$playlist = $_POST["playlist"];
		$ret = $rdio->get([
			'keys'=>$playlist,
			'extras'=>'trackKeys'
			]);

		if ($ret->status == 'ok')
		{
			$trackarr =& $ret->result->$playlist->trackKeys;
			$tracks = $rdio->get(['keys'=>implode(",", $trackarr)])->result;
			ob_start();
			require('templates/playlist.php');
			$html = ob_get_clean();
			json_output(['status'=>'ok', 'html'=>$html]);
		}
	}

	json_output($ret);
}
elseif (isset($_GET['playlist/saveas']))
{
	$ret = $rdio->createPlaylist([
		'name'=>$_POST["newname"],
		'description'=>$_POST["newname"],
		'tracks'=>implode(",", $_POST['tracks'])
		]);

	if ($ret->status == 'ok')
	{
		$p = $ret->result;
		ob_start();
		include("templates/home_playlist_rec.php");
		$html = ob_get_clean();
		json_output(['status'=>'ok', 'html'=>$html]);
	}

	json_output($ret);
}
elseif (isset($_GET['playlist/delete']))
{
	$ret = $rdio->deletePlaylist([
		'playlist'=>$_POST["playlist"]
		]);

	json_output($ret);
}
elseif (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
	json_output(['status'=>'unknown method call']);
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

function json_output($data)
{
	header('Content-Type: application/json');
	echo json_encode($data);
	die;
}

function array_swap(&$array, $a, $b)
{
	// adapted from moveElement() by http://stackoverflow.com/users/367456/hakre
	// http://stackoverflow.com/questions/12624153/move-an-array-element-to-a-new-index-in-php
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
}
