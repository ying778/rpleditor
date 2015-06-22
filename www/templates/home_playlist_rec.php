<tr class="playlist_row1" playlist="<?=htmlentities($p->key)?>">
	<td sorttable_customkey="<?=htmlentities($p->name)?>">
		<img src="<?=$p->icon?>" style="width:50px">
	</td>
	<td><span class="playlist_name"><?=htmlentities($p->name)?></span></td>
	<td style="text-align:right"><?=htmlEntities($p->length)?></td>
	<td><div class="playlist_buttons">
		<button class="btn btn-small btnEdit"><i class="icon-edit"></i> Edit</button>
		<button class="btn btn-small btnSave" style="display:none"><i class="icon-file"></i> Save</button>
		<button class="btn btn-small btnSaveAs" style="display:none"><i class="icon-file"></i> Save As</button>
		<button class="btn btn-small btnDelete" style="margin-left:10px"><i class="icon-trash"></i> Delete</button>
		<span class="messages" style="margin-left:10px"></span>
		</div>
	</td>
</tr>
<tr class="playlist_row2">
	<td style="border-top:none"></td>
	<td style="border-top:none" colspan="3" class="tracks"></td>
</tr>
