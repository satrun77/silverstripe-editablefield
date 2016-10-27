<li class="field $ClassName Moo_EditableField" id="$Name.ATT EditableItem_$Pos $Name">
	<div class="fieldInfo">
		<img class="icon" src="$Icon" alt="$ClassName" title="$singular_name" />

		$NameField $TitleField
	</div>

	<div class="fieldActions">
		<% if showExtraOptions %>
		<a class="moreOptions" href="#" title="<% _t('Moo_EditableField.SHOWOPTIONS', 'Show Options') %>"><% _t('Moo_EditableField.SHOWOPTIONS','Show Options') %></a>
		<% end_if %>

		<a class="delete" href="#" title="<% _t('Moo_EditableField.DELETE', 'Delete') %>"><% _t('Moo_EditableField.DELETE', 'Delete') %></a>
		<a class="save" href="#" title="<% _t('Moo_EditableField.SAVE', 'Save') %>"><% _t('Moo_EditableField.SAVE', 'Save') %></a>
	</div>

	<% if showExtraOptions %>
	<div class="extraOptions hidden" id="$Name.ATT-extraOptions">
		<% if HasAddableOptions %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('Moo_EditableField.OPTIONS', 'Options') %></legend>
			<ul class="editableFieldOptions" id="$FieldName.ATT-list">
				<% loop Options %>
				$EditSegment
				<% end_loop %>
				<% if HasAddableOptions %>
				<li class="{$ClassName}Option">
					<a href="#" rel="$ID" class="addableOption" title="<% _t('Moo_EditableField.ADD', 'Add option to field') %>">
						<% _t('Moo_EditableField.ADDLabel', 'Add option') %>
					</a>
				</li>
				<% end_if %>
			</ul>
		</fieldset>
		<% end_if %>

		<% if FieldConfiguration %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('Moo_EditableField.FIELDCONFIGURATION', 'Field Configuration') %></legend>
			<% loop FieldConfiguration %>
			$FieldHolder
			<% end_loop %>
		</fieldset>
		<% end_if %>

		<% if FieldValidationOptions %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('Moo_EditableField.VALIDATION', 'Validation') %></legend>
			<% loop FieldValidationOptions %>
			$FieldHolder
			<% end_loop %>
		</fieldset>
		<% end_if %>
	</div>
	<% end_if %>

	<!-- Hidden option Fields -->
	<input type="hidden" class="typeHidden" name="{$FieldName}[Type]" value="$ClassName" />
</li>
