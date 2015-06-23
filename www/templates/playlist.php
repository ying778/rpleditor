<div class="tracklist_container">
<p style="font-size:12px"><span class="label label-info">Instructions</span> Click column headings to sort. Drag &amp; drop rows to reorder. Remember to Save.</p>

<div class="track_buttons" style="margin-bottom:5px">
<button class="btn btn-mini btnShuffle"><i class="icon-random"></i> Shuffle Tracks</button>
<button class="btn btn-mini btnDeDupe"><i class="icon-minus"></i> Find Duplicates</button>
</div>

<form>
<input type="hidden" name="playlist" value="<?=htmlEntities($playlist)?>">
<table class="table table-condensed sortable tracklist" id="playlist<?=htmlEntities($playlist)?>" style="font-size:12px">
<thead>
<tr>
	<th style="width:10px">Track</th>
	<th>Title</th>
	<th>Artist</th>
	<th>Album</th>
	<th>Duration</th>
	<th>Status</th>
	<th style="width:20px" class="sorttable_nosort">Delete</th>
</tr>
</thead>
<tbody>
<?$i=0?>
<?foreach ($trackarr as $tkey) {?>
	<?$t =& $tracks->$tkey?>
	<tr index="<?=$i?>" track="<?=htmlEntities($t->key)?>" <?if (!$t->canStream) {?>class="unavailable"<?}?>>
		<td sorttable_customkey="<?=$i?>">
			<?=++$i?>
			<input type="hidden" name="tracks[]" value="<?=htmlEntities($t->key)?>">
		</td>
		<td class="name"><?=htmlentities($t->name)?></td>
		<td class="artist"><?=htmlentities($t->artist)?></td>
		<td><?=$t->album?></td>
		<td sorttable_customkey="<?=$t->duration?>" style="text-align:right"><?=sprintf("%d:%02d", floor($t->duration/60), ($t->duration%60))?></td>
		<td sorttable_customkey="<?=$t->isExplicit?'E':'-'?><?=$t->canStream?'-':'U'?>">
			<?if ($t->isExplicit) {?><span class="label label-important" style="font-size:9px">Explicit</span><?}?>
			<?if (!$t->canStream) {?><span class="label label-warning" style="font-size:9px">Unavailable</span><?}?>
		</td>
		<td style="text-align:center">
			<input class="chkdelete" type="checkbox" name="delete[]" value="<?=htmlEntities($t->key)?>">
		</td>
	</tr>
<?}?>
</tbody>
</table>
</form>
</div>

<script>
$(document).ready(function() {
	tbl = $('#playlist<?=htmlEntities($playlist)?>')[0];
	sorttable.makeSortable(tbl);

	$('input.chkdelete').click(function() {
		var row = $(this).closest('tr');
		if (this.checked) {
			row.addClass('delete');
		} else {
			row.removeClass('delete');
		}
	});

	$(tbl).find('tbody')
		.sortable({
			placeholder:"placeholder",
			helper:function(e, ui) {
				ui.children().each(function() {
					$(this).width($(this).width());
				});
				return ui;
			},
			start:function(e, ui) {
				$(ui.item).addClass('moving');
			},
			stop:function(e, ui) {
				$(ui.item).removeClass('moving');
				if ($(ui.item).attr('index') != $(ui.item).index()) {
					$(ui.item).addClass('moved');
				} else {
					$(ui.item).removeClass('moved');
				}
			},
			axis:'y',
			cursor:'move',
			containment:"parent"
		});
});
</script>
