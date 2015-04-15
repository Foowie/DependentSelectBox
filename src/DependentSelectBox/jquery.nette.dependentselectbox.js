/**
 * @author Daniel Robenek
 * @license MIT
 */

/**
 * 	$(document).ready(function() {
 *		$.dependentselectbox.initialize();
 *	});
 *
 *	Add to jquery.nette.js at the end of $.nette.success:
 *	$.dependentselectbox.hideSubmits();
 *	or use livequery
 */
jQuery.extend({
	dependentselectbox: {
		controlClass: 'dependentControl',

		buttonSuffix: '_submit',

		hideSubmits: function() {
			// Here hide all you want. Default is to hide <tr> of button
			$('.'+$.dependentselectbox.controlClass+$.dependentselectbox.buttonSuffix).parent().parent().hide();
		},

		initialize: function() {
			$.dependentselectbox.hideSubmits();
			$(document).on('change', function (e) {
				if ($(e.target).is('.'+$.dependentselectbox.controlClass)) {
					// Nette form validation
					button = document.getElementById((e.currentTarget.activeElement.id) + $.dependentselectbox.buttonSuffix);
					button.form["nette-submittedBy"] = button;
					// ----
					$('#' + (e.currentTarget.activeElement.id) + $.dependentselectbox.buttonSuffix).ajaxSubmit($.dependentselectbox.jsonResponse);
				}
			});
		},

		updateSelectBox: function(id, selectedKey, items) {
			$("#" + id + " option").remove();
			var select = $("#" + id);
			for (var i = 0; i < items.keys.length; i++) {
				var key = items.keys[i];
				var item = $("<option></option>").attr("value", key).html(items.values[i]);
				if (key === selectedKey)
					item.attr("selected", "selected");
				if (key === "")
					select.prepend(item);
				else
					select.append(item);
			}
		},

		jsonResponse: function(payload) {
			if(!(payload["type"] && payload["type"] == "JsonDependentSelectBoxResponse")) {
				$.nette.success(payload);
				return;
			}
			var items = payload["items"];
			for(var i in items) {
				$.dependentselectbox.updateSelectBox(i, items[i]["selected"], items[i]["items"]);
			}
		}
	}
});

$(document).ready(function() {
	$.dependentselectbox.initialize();

});

