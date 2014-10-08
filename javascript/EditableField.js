/**
 *
 * @author silverstripe/userforms
 * @author Mohamed Alsharaf
 */
(function($) {
	$(document).ready(function() {
		var editablefield = editablefield || {};

		/**
		 * Messages from EditableField are translatable using i18n.
		 */
		editablefield.messages = {
			ERROR_CREATING_FIELD: 'Error creating field',
			ADDING_FIELD: 'Adding new field',
			UPDATING_FIELD: 'Field updated successfully',
			ADDED_FIELD: 'Added new field',
			HIDE_OPTIONS: 'Hide options',
			SHOW_OPTIONS: 'Show options',
			ADDING_OPTION: 'Adding option',
			ADDED_OPTION: 'Added option',
			ERROR_CREATING_OPTION: 'Error creating option',
			REMOVED_OPTION: 'Removed option',
			ADDING_RULE: 'Adding rule'
		};

		/**
		 * Returns a given translatable string from a passed key. Keys should be
		 * all caps without any spaces.
		 */
		editablefield.message = function() {
			en = arguments[1] || editablefield.messages[arguments[0]];
			return ss.i18n._t("EditableField." + arguments[0], en);
		};

		/**
		 * Format url
		 */
		editablefield.appendToURL = function(url, pathsegmenttobeadded) {
			var parts = url.match(/([^\?#]*)?(\?[^#]*)?(#.*)?/);
			for(var i in parts) {
				if(!parts[i]) {
					parts[i] = '';
				}
			}
			return parts[1] + pathsegmenttobeadded + parts[2] + parts[3];
		};

		/**
		 * Get form security ID
		 */
		editablefield.getSecurityID = function(form) {
			form = (typeof form === "undefined") ? null : form;
			if(form === null) {
				return $("input[name=SecurityID]").first().val();
			}
			return form.find('input[name=SecurityID]').val();
		};

		/**
		 * Update the sortable properties of the form as a function since the
		 * application will need to refresh the UI dynamically
		 */
		editablefield.update = function() {
			$(".editableFieldOptions").sortable({
				handle: '.handle',
				cursor: 'pointer',
				items: 'li',
				placeholder: 'removed-form-field',
				opacity: 0.6,
				revert: true,
				change: function(event, ui) {
					$(this).sortable('refreshPositions');
				},
				update: function(event, ui) {
					var sort = 1;
					$(".editableFieldOptions li").each(function() {
						$(this).find(".sortOptionHidden").val(sort++);
					});
				}
			});
		};

		/**
		 * Workaround for not refreshing the sort.
		 *
		 * TODO: better solution would to not fire this on every hover but needs
		 * to ensure it doesn't have edge cases. The sledge hammer approach.
		 */
		$(".fieldHandler, .handle").live('hover', function() {
			editablefield.update();
		});
		editablefield.update();

		/**
		 * Create a new instance of a field
		 */
		$('#Form_AddNewForm .action').entwine({
			onclick: function(e) {
				var form = $(this).closest('form');
				var length = $(".fieldInfo").length + 1;
				var formData = form.serialize() + '&NewID=' + length;
				var addURL = form.attr('action');

				e.preventDefault();
				$.ajax({
					headers: {"X-Pjax": 'Partial'},
					type: "POST",
					url: addURL,
					data: formData,
					success: function(data) {
						$('#Fields_fields').append(data);
						statusMessage(editablefield.message('ADDED_FIELD'));
						$("#Fields_fields li.EditableField:last").effect('highlight');
						var name = $("#Fields_fields li.EditableField:last").attr("id").split(' ');
						$("#Fields_fields select.fieldOption").append("<option value='" + name[2] + "'>New " + name[2] + "</option>");
					},
					error: function(e) {
					}
				});
			}
		});

		/**
		 * Filter fields list
		 */
		$('#Form_SearchForm .action').entwine({
			onclick: function(e) {
				var form = $(this).closest('form');
				var formData = form.serialize();
				var url = form.attr('action');

				e.preventDefault();
				$.ajax({
					headers: {"X-Pjax": 'CurrentForm'},
					type: "POST",
					url: url,
					data: formData,
					success: function(data) {
						$('#Form_EditForm .cms-content-fields').html($(data.CurrentForm).find('.cms-content-fields').html());
					},
					error: function(e) {
					}
				});
			}
		});

		/**
		 * Update details of a field
		 */
		$('.EditableField .save').entwine({
			onclick: function(e) {
				var form = $(this).parents('.EditableField');
				var formData = form.find(':input').serialize() + '&save_row=1&action_save=1&&SecurityID=' + editablefield.getSecurityID();
				var addURL = $(this).closest('form').attr('action');

				e.preventDefault();

				$.ajax({
					headers: {"X-Pjax": 'CurrentForm'},
					type: "POST",
					url: addURL,
					data: formData,
					success: function(data) {
						var nameField = form.find('.text').first();
						nameField.val($(data.CurrentForm).find('#' + nameField.attr('id')).val());
						form.effect('highlight');
					},
					error: function(e) {
					}
				});
			}
		});

		/**
		 * Delete a field
		 */
		$(".EditableField .delete").entwine({
			onclick: function(e) {
				e.preventDefault();
				var form = $(this).parents('.EditableField');
				var formData = form.find(':input').serialize() + '&delete_row=1&action_delete=1&&SecurityID=' + editablefield.getSecurityID();
				var addURL = $(this).closest('form').attr('action');
				var remove = function(data) {
					var text = $(this).parents("li").find(".fieldInfo .text").val();

					// Remove all the rules with relate to this field
					$("#Fields_fields .customRules select.fieldOption option").each(function(i, element) {
						if($(element).text() === text) {
							// check to see if this is selected. If its then remove the whole rule
							if($(element).parent('select.customRuleField').val() === $(element).val()) {
								$(element).parents('li.customRule').remove();
							} else {
								// otherwise remove the option
								$(element).remove();
							}
						}
					});
					$(this).parents(".EditableField").slideUp(function() {
						$(this).remove();
					});
				};
				e.preventDefault();

				$.ajax({
					headers: {"X-Pjax": 'CurrentForm'},
					type: "POST",
					url: addURL,
					data: formData,
					success: $.proxy(remove, this),
					error: function(e) {
					}
				});
			}
		});

		/**
		 * Upon renaming a field we should go through and rename all the fields in the select fields to
		 * use this new field title. We can just worry about the title text - don't mess around with the keys
		 */
		$('.EditableField .fieldInfo .text').entwine({
			onchange: function(e) {
				var value = $(this).val();
				var name = $(this).parents("li").attr("id").split(' ');
				$("#Fields_fields select.fieldOption option").each(function(i, domElement) {
					if($(domElement).val() === name[2]) {
						$(domElement).text(value);
					}
				});
			}
		});

		/**
		 * Show the more options popdown. Or hide it if we currently have it open
		 */
		$(".EditableField .moreOptions").entwine({
			onclick: function(e) {
				e.preventDefault();

				var parent = $(this).parents(".EditableField");
				if(!parent) {
					return;
				}

				var extraOptions = parent.children(".extraOptions");
				if(!extraOptions) {
					return;
				}

				if(extraOptions.hasClass('hidden')) {
					$(this).addClass("showing");
					$(this).html('Hide options');
					extraOptions.removeClass('hidden');
				} else {
					$(this).removeClass("showing");
					$(this).html('Show options');
					extraOptions.addClass('hidden');
				}
			}
		});

		/**
		 * Add a suboption to a radio field or to a dropdown box for example
		 */
		$(".EditableField .addableOption").entwine({
			onclick: function(e) {
				e.preventDefault();

				// Give the user some feedback
				statusMessage(editablefield.message('ADDING_OPTION'));

				// variables
				var options = $(this).parent("li");
				var action = editablefield.appendToURL($("#Form_EditForm").attr("action"), '/?action_addoptionfield=1');
				var parent = $(this).attr("rel");

				// send ajax request
				$.ajax({
					type: "GET",
					url: action,
					data: 'Parent=' + parent + '&SecurityID=' + editablefield.getSecurityID(),
					// create a new field option
					success: function(msg) {
						options.before(msg);
						statusMessage(editablefield.message('ADDED_OPTION'));
					},
					error: function(request, text, error) {
						statusMessage(editablefield.message('ERROR_CREATING_OPTION'));
					}
				});
			}
		});

		/**
		 * Delete a suboption such as an dropdown option or a checkbox field
		 */
		$(".EditableField .deleteOption").entwine({
			onclick: function(e) {
				e.preventDefault();

				// pass the deleted status onto the element
				$(this).parents("li:first").find("[type=text]:first").attr("value", "field-node-deleted");
				$(this).parents("li:first").hide();

				// User status message
				statusMessage(editablefield.message('REMOVED_OPTION'));
			}
		});
	});
})(jQuery);
