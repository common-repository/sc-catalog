/*
	SC Catalog
	Copyright 2011 Cédric Houbart (email : cedric@scil.coop)
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Script to handle calatog display effects


// Auto resize details height
var details_height = Math.round(jQuery(window).height() * 0.8);
jQuery(window).resize(function() {
	details_height = Math.round(jQuery(window).height() * 0.8);
	jQuery("a.thickbox.more-details").attr("href", "./#TB_inline?height=" + details_height + "&width=600&inlineId=sc-catalog-details");
});

jQuery(document).ready(function() {
	jQuery("a.thickbox.more-details").attr("href", "./#TB_inline?height=" + details_height + "&width=600&inlineId=sc-catalog-details");
	jQuery(".sc-catalog-list-item a.thickbox").click(function() {
		// Collect data
		var li = jQuery(this).parent().parent();
		var id = li.attr('data-id');
		var has_image = li.children("img").length > 0;
		if ( has_image ) {
			var img = li.children("img").attr("src");
		}
		var title = li.children("h3").html();
		var has_catch_phrase = li.children("p").length > 0;
		if ( has_catch_phrase ) {
			var catch_phrase = li.children("p").html();
		}
		var has_text = li.children("div.sc-catalog-text").length > 0;
		if ( has_text ) {
			var text = li.children("div.sc-catalog-text").html();
		}
		// Show data
		var details = "";
		if ( has_image ) {
			details += '<img src="' + img + '" />';
		}
		details += "<h3>" + title + "</h3>";
		if ( has_catch_phrase ) {
			details += '<p class="sc-catalog-catch-phrase">' + catch_phrase + "</p>";
		}
		if ( has_text ) {
			details += text;
		}
		jQuery(".sc-catalog-details").html(details);
		return true;
		});
	});

