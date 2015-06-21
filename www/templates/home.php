<div class="container">

<div class="row" style="border-bottom:1px solid #c0c0c0;margin-bottom:20px">
	<div class="span12">
		<h3>
			<?=htmlentities($_SESSION["user"]->firstName)?>'s Playlists
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
</div>
<script>
$(document).ready(function() {
	$('.editbutton').click(function() {
		var me = $(this);
		var row = me.closest('tr');
		var playlist = me.closest('[playlist]').attr('playlist');
		me.data('orightml', me.html()).text('Loading...').attr('disabled', true);
		row.next('tr').find('td.edit').load('<?=WWWROOT?>/?playlist='+playlist, function() {
			me.html(me.data('orightml')).attr('disabled', false);
			me.parent().find('.savebutton').show();
		});
	});

	$('.savebutton').click(function() {
		var me = $(this);
		var row = me.closest('tr');
		var playlist = me.closest('[playlist]').attr('playlist');
		me.data('orightml', me.html()).text('Saving...').attr('disabled', true);
		me.next('.label').remove();
		params = row.next('tr').find('form').serialize();
		$.post('<?=WWWROOT?>/?save', params, function(data, textStatus, jqXHR) {
			if (data.status == 'ok') {
				row.next('tr').find('td.edit').load('<?=WWWROOT?>/?playlist='+playlist, function() {
					me.html(me.data('orightml')).attr('disabled', false);
					me.parent().find('.savebutton').show();
					me.parent().find('.savebutton').after('<span class="label label-success" style="margin-left:10px">Saved</span>');
				});
			}
		}, 'JSON');
	});
});
</script>

</div> <?//container?>
