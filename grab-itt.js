jQuery(function() {
  jQuery(".delete").click(function() {
    return confirm("Are you sure?");
  });
  jQuery("table.data tr:odd").addClass("odd");
	jQuery("table.data tr:even").addClass("even");
});