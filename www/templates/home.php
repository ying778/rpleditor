<div class="container">

<div class="row" style="margin-bottom:20px">
	<div class="span8 lead">
		<img src="<?=$_SESSION["user"]->icon?>" width="50" height="50">
		<?=htmlentities($_SESSION["user"]->firstName)?>'s Rdio Playlists
	</div>
	<div class="span4" style="text-align:right">
		<a class="btn" href="<?=WWWROOT?>">Refresh</a>
		<a class="btn" href="<?=WWWROOT?>/?logout">Logout</a>
	</div>
</div>
<div class="row">
	<div class="span12">
		<table class="table playlist">
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
	$('#playlist_container').on('click', '.playlist_buttons .btnEdit', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');
		var playlistbody = row.next('tr').find('.tracks');

		me.attr('disabled', true);
		msg.html('<span class="label label-info">Loading tracks...</span>');

		playlistbody.addClass('faded').load('<?=WWWROOT?>/?playlist='+playlist, function() {
			me.attr('disabled', false);
			msg.html('');
			me.parent().find('.btnSave').show().attr('disabled', false);
			me.parent().find('.btnSaveAs').show().attr('disabled', false);
			$('body').animate({scrollTop:row.offset().top}, 250);
			playlistbody.removeClass('faded');
		});
	});

	$('#playlist_container').on('click', '.playlist_buttons .btnSave', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');
		var playlistbody = row.next('tr').find('.tracks');
		var form = row.next('tr').find('form');
		var deltracks = form.find('input.chkdelete:checked');

		if (deltracks.length == 0 || confirm('Save playlist order and delete '+deltracks.length+' tracks?')) {
			var params = form.serializeArray();

			me.attr('disabled', true);
			msg.html('<span class="label label-info">Saving...</span>');
			playlistbody.addClass('faded');

			$.post('<?=WWWROOT?>/?playlist/save', params, function(data, textStatus, jqXHR) {
				if (data.status == 'ok') {
					playlistbody.html(data.html).removeClass('faded');
					me.attr('disabled', false);
					msg.html('<span class="label label-success">Saved</span>');
				}
			}, 'JSON');
		}
	});

	$('#playlist_container').on('click', '.playlist_buttons .btnSaveAs', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');

		var newname = 'Copy of '+row.find('.playlist_name').text();
		if (newname = prompt('Enter new playlist name', newname)) {
			var params = row.next('tr').find('form').serializeArray();
			params.push({name:"newname", value:newname});

			me.attr('disabled', true);
			msg.html('<span class="label label-info">Saving...</span>');
			$.post('<?=WWWROOT?>/?playlist/saveas', params, function(data, textStatus, jqXHR) {
				me.attr('disabled', false);
				if (data.status == 'ok') {
					msg.html('');

					var newrow = $(data.html);
					$('body').animate({scrollTop:$('#playlist_container').offset().top}, 250);
					$('#playlist_container').prepend(newrow);
					newrow.find('.playlist_name').after('<span class="label label-success" style="margin-left:10px">New</span>');
					newrow.find('.btnEdit').trigger('click');
				}
			}, 'JSON');
		}
	});

	$('#playlist_container').on('click', '.playlist_buttons .btnDelete', function() {
		var me = $(this);
		var row = me.closest('tr');
		var msg = me.parent().find('.messages');
		var playlist = me.closest('[playlist]').attr('playlist');

		if ('OK' == prompt('Delete playlist "'+row.find('.playlist_name').text()+'".\n\nType OK to proceed (all uppercase).', '')) {
			me.attr('disabled', true);
			msg.html('<span class="label label-info">Deleting...</span>');
			var params = {playlist:playlist}
			$.post('<?=WWWROOT?>/?playlist/delete', params, function(data, textStatus, jqXHR) {
				me.attr('disabled', false);
				if (data.status == 'ok') {
					var trackrow = row.next('tr');
					row.fadeOut(function() {
						row.remove();
					});
					trackrow.fadeOut(function() {
						trackrow.remove();
						$('body').scrollTop(0);
					});
				}
			}, 'JSON');
		}
	});
});
</script>

</div> <?//container?>
