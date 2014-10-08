<li>
	<img class="handle" src="$ModulePath(framework)/images/drag.gif" alt="<% _t('EditableFieldOption.DRAG', 'Drag to rearrange order of options') %>" />
	<input type="text" name="{$FieldName}[Title]" value="$Title" />
	<input type="hidden" class="sortOptionHidden hidden" name="{$FieldName}[Sort]" value="$Sort" />

	<a href="$ID" class="deleteOption"><img src="$ModulePath(framework)/images/delete.gif" alt="<% _t('EditableFieldOption.DELETE', 'Remove this option') %>" /></a>
</li>