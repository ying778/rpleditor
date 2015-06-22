<div class="container">

<div class="row" style="border-bottom:1px solid #c0c0c0;margin-bottom:20px">
	<div class="span12">
		<h3>
			<img src="<?=$_SESSION["user"]->icon?>" width="50" height="50">
			<?=htmlentities($_SESSION["user"]->firstName)?>'s Rdio Playlists
			<a class="btn pull-right" href="<?=WWWROOT?>/?logout">Logout</a>
			<a class="btn pull-right" href="<?=WWWROOT?>" style="margin-right:5px">Refresh</a>
		</h3>
	</div>
</div>
<div class="row">
	<div class="span12">
		<table class="table">
		<thead>
		<tr>
			<th style="width:50px"></th>
			<th style="width:300px">Playlist</th>
			<th style="width:50px">Songs</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody id="playlist_container">
		<?foreach ($myPlaylists as $p) {?>
			<?include("home_playlist_rec.php")?>
		<?}?>
		</tbody>
		</table>
	</div>
</div>
<script>
$(document).ready(function() {
	$('#playlist_container .playlist_buttons').on('click', '.btnEdit', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');
		var playlistbody = row.next('tr').find('td.edit');

		me.attr('disabled', true);
		msg.html('<span class="label label-info">Loading...</span>');

		playlistbody.addClass('faded').load('<?=WWWROOT?>/?playlist='+playlist, function() {
			me.attr('disabled', false);
			msg.html('');
			me.parent().find('.btnSave').show().attr('disabled', false);
			me.parent().find('.btnSaveAs').show().attr('disabled', false);
			$('body').animate({scrollTop:row.offset().top}, 250);
			playlistbody.removeClass('faded');
		});
	});

	$('#playlist_container .playlist_buttons').on('click', '.btnSave', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');
		var params = row.next('tr').find('form').serializeArray();

		me.attr('disabled', true);
		msg.html('<span class="label label-info">Saving...</span>');

		$.post('<?=WWWROOT?>/?playlist/save', params, function(data, textStatus, jqXHR) {
			if (data.status == 'ok') {
				row.next('tr').find('td.edit').load('<?=WWWROOT?>/?playlist='+playlist, function() {
					me.attr('disabled', false);
					msg.html('<span class="label label-success">Saved</span>');
				});
			}
		}, 'JSON');
	});

	$('#playlist_container .playlist_buttons').on('click', '.btnSaveAs', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');

		var newname = 'Copy of '+row.find('td.name').text();
		if (newname = prompt('Enter new playlist name', newname)) {
			var params = row.next('tr').find('form').serializeArray();
			params.push({name:"newname", value:newname});

			me.attr('disabled', true);
			msg.html('<span class="label label-info">Saving...</span>');
			$.post('<?=WWWROOT?>/?playlist/saveas', params, function(data, textStatus, jqXHR) {
				me.attr('disabled', false);
				if (data.status == 'ok') {
					msg.html('');

					var newrow = $(data.playlist_row_html);
					$('body').animate({scrollTop:$('#playlist_container').offset().top}, 250);
					$('#playlist_container').prepend(newrow);
					newrow.find('.messages').html('<span class="label label-success">New playlist created</span>');
					newrow.find('.editbutton').trigger('click');
				}
			}, 'JSON');
		}
	});

	$('#playlist_container .playlist_buttons').on('click', '.btnDelete', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');

		if ('OK' == prompt('Delete playlist "'+row.find('td.name').text()+'".\n\nType OK to proceed.', '').toUpperCase()) {
			me.attr('disabled', true);
			msg.html('<span class="label label-info">Deleting...</span>');
			var params = {playlist:playlist}
			$.post('<?=WWWROOT?>/?playlist/delete', params, function(data, textStatus, jqXHR) {
				me.attr('disabled', false);
				if (data.status == 'ok') {
					row.fadeOut(function() {
						row.remove();
					});
				}
			}, 'JSON');
		}
	});
});
</script>

</div> <?//container?>
