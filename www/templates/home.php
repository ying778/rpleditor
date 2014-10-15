<div class="container">

<div class="row" style="border-bottom:1px solid #c0c0c0;margin-bottom:20px">
	<div class="span12">
		<h3>
			<?=htmlentities($currentUser->result->firstName)?>'s Playlists
			<a class="btn pull-right" href="<?=WWWROOT?>/?logout">Logout</a>
			<a class="btn pull-right" href="<?=WWWROOT?>">Refresh</a>
		</h3>
	</div>
</div>
<div class="row">
	<div class="span12">
		<table class="table">
		<thead>
		<tr>
			<th style="width:50px"></th>
			<th style="width:50px">Playlist</th>
			<th style="width:50px">Songs</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
		<?foreach ($myPlaylists as $p) {?>
			<tr playlist="<?=htmlentities($p->key)?>">
				<td sorttable_customkey="<?=htmlentities($p->name)?>">
					<img src="<?=$p->icon?>" style="width:50px">
				</td>
				<td><?=htmlentities($p->name)?></td>
				<td><?=htmlEntities($p->length)?></td>
				<td>
					<div>
					<button class="btn btn-small editbutton"><i class="icon-edit"></i> Edit</button>
					<button class="btn btn-small savebutton" style="display:none"><i class="icon-file"></i> Save</button>
					<button class="btn btn-small shufflebutton" style="display:none"><i class="icon-random"></i> Save</button>
<?/*					<button class="btn btn-small playerbutton" embedurl="<?=htmlentities($playlist->embedUrl)?>"><i class="icon-play"></i> Play</button>*/?>
					</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="3" class="edit"></td>
			</tr>
		<?}?>
		</tbody>
		</table>
	</div>
<?/*
	<div class="span4">
		<h3>Player</h3>
		<div id="player" style="background-color:#40535E;height:400px;color:#ffffff"><p style="padding:10px">No playlist selected</p></div>
	</div>
*/?>
</div>
<script>
<?/*
$('.playerbutton').click(function() {
	me = $(this);
<?//	$('#player').html('<iframe style="height:400px" src="'+me.attr('embedurl')+'"></iframe><div><button class="btn btn-small" onclick="window.open(\''+me.attr('embedurl')+'\')"><i class="icon-fullscreen"></i> Open Fullsize</button>');?>
	window.open(me.attr('embedurl'));
});
*/?>

$('.editbutton').click(function() {
	var me = $(this);
	var row = me.closest('tr');
	var playlist = me.closest('[playlist]').attr('playlist');
	me.data('orightml', me.html()).text('Loading...').attr('disabled', true);
	row.next('tr').find('td.edit').load('<?=WWWROOT?>/?playlist='+playlist, function() {
		me.html(me.data('orightml')).attr('disabled', false);
		me.parent().find('.savebutton').show();
		me.parent().find('.shufflebutton').show();
	});
});

// http://jsfiddle.net/98q9S/
function shuffleRows(parent) {
    var rows = parent.children();
    for (var i = rows.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = rows[i];
        rows.eq(i - 1).after(rows[j]);
        rows.eq(j - 1).after(temp);
    }
}

$('.shufflebutton').click(function () {
	var playlist = $(this).closest('tr').next('tr').find('table.playlist tbody');
  shuffleRows(playlist);
  playlist.find('tr').addClass('moved');
});

$('.savebutton').click(function() {
	var me = $(this);
	var row = me.closest('tr');
  var playlist = me.closest('[playlist]').attr('playlist');
  me.parent().find('.shufflebutton').attr('disabled', true);
	me.data('orightml', me.html()).text('Saving...').attr('disabled', true);
	me.next('.label').remove();
	params = row.next('tr').find('form').serialize();
	$.post('<?=WWWROOT?>/?save', params, function(data, textStatus, jqXHR) {
		if (data.status == 'ok') {
			row.next('tr').find('td.edit').load('<?=WWWROOT?>/?playlist='+playlist, function() {
				me.html(me.data('orightml')).attr('disabled', false);
				me.parent().find('.savebutton').show();
				me.parent().find('.savebutton').after('<span class="label label-success" style="margin-left:10px">Saved</span>');
        me.parent().find('.shufflebutton').attr('disabled', false);
			});
		}
	}, 'JSON');
});
</script>

</div> <?//container?>
