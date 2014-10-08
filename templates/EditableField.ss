<li class="field $ClassName EditableField" id="$Name.ATT EditableItem_$Pos $Name">
	<div class="fieldInfo">
		<img class="icon" src="$Icon" alt="$ClassName" title="$singular_name" />

		$NameField $TitleField
	</div>

	<div class="fieldActions">
		<% if showExtraOptions %>
		<a class="moreOptions" href="#" title="<% _t('EditableField.SHOWOPTIONS', 'Show Options') %>"><% _t('EditableField.SHOWOPTIONS','Show Options') %></a>
		<% end_if %>

		<a class="delete" href="#" title="<% _t('EditableField.DELETE', 'Delete') %>"><% _t('EditableField.DELETE', 'Delete') %></a>
		<a class="save" href="#" title="<% _t('EditableField.SAVE', 'Save') %>"><% _t('EditableField.SAVE', 'Save') %></a>
	</div>

	<% if showExtraOptions %>
	<div class="extraOptions hidden" id="$Name.ATT-extraOptions">
		<% if HasAddableOptions %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('EditableField.OPTIONS', 'Options') %></legend>
			<ul class="editableFieldOptions" id="$FieldName.ATT-list">
				<% loop Options %>
				$EditSegment
				<% end_loop %>
				<% if HasAddableOptions %>
				<li class="{$ClassName}Option">
					<a href="#" rel="$ID" class="addableOption" title="<% _t('EditableField.ADD', 'Add option to field') %>">
						<% _t('EditableField.ADDLabel', 'Add option') %>
					</a>
				</li>
				<% end_if %>
			</ul>
		</fieldset>
		<% end_if %>

		<% if FieldConfiguration %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('EditableField.FIELDCONFIGURATION', 'Field Configuration') %></legend>
			<% loop FieldConfiguration %>
			$FieldHolder
			<% end_loop %>
		</fieldset>
		<% end_if %>

		<% if FieldValidationOptions %>
		<fieldset class="fieldOptionsGroup">
			<legend><% _t('EditableField.VALIDATION', 'Validation') %></legend>
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
