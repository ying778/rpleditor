<form playlist="<?=htmlEntities($_GET['playlist'])?>">
<input type="hidden" name="playlist" value="<?=htmlEntities($_GET['playlist'])?>">
<p style="font-size:12px"><span class="label label-info">Instructions</span> Click column headings to sort. Drag &amp; drop rows to reorder. Remember to Save.</p>
<table class="table table-condensed sortable" id="playlist<?=htmlEntities($_GET['playlist'])?>" style="font-size:12px">
<thead>
<tr>
	<th style="width:10px">Track</th>
	<th>Title</th>
	<th>Artist</th>
	<th>Album</th>
	<th style="width:20px"></th>
</tr>
</thead>
<tbody>
<?$i=0?>
<?foreach ($trackarr as $tkey) {?>
	<?$t =& $tracks->$tkey?>
	<tr index="<?=$i?>" track="<?=htmlEntities($t->key)?>">
		<td sorttable_customkey="<?=$i?>">
			<?=++$i?>
			<input type="hidden" name="keys[]" value="<?=htmlEntities($t->key)?>">
		</td>
		<td>
			<?=htmlentities($t->name)?>
			<?if ($t->isExplicit) {?><span class="label label-important" style="font-size:9px">Explicit</span><?}?>
		</td>
		<td><?=htmlentities($t->artist)?></td>
		<td><?=$t->album?></td>
		<td sorttable_customkey="<?=$i?>"><a class="close delete" style="display:none" title="Delete">&times;</a></td>
	</tr>
<?}?>
</tbody>
</table>
</form>
<script>
$(document).ready(function() {
	tbl = $('#playlist<?=htmlEntities($_GET['playlist'])?>')[0];
	sorttable.makeSortable(tbl);

	$('a.delete').click(function() {
		var me = $(this);
		var index = $(this).closest('[index]').attr('index');
		var track = $(this).closest('[track]').attr('track');
		var playlist = me.closest('[playlist]').attr('playlist');
		var params = {playlist:playlist, index:index, track:track};
		$.post('<?=WWWROOT?>/?delete', params, function(data, textStatus, jqXHR) {
			if (data.status == 'ok') {
				me.closest('tr').fadeOut();
			}
		}, 'JSON');
	});

	$(tbl).find('tr').hover(
		function() {$(this).find('.close').show()},
		function() {$(this).find('.close').hide()}
	);

	$(tbl).find('>*')
		.sortable({
			helper: function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			},
			axis:'y',
			cursor:'move',
		});
});
</script>