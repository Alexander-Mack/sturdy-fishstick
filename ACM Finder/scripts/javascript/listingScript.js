// wait for the document to be ready
$(document).ready(() => {
    var ss = sessionStorage.getItem(-2);
    if (ss != null) {
        $("#supplierSelect").val(ss);
    }
    // event listener for keypress on the search table input
    document.getElementById("searchTableField").addEventListener("keypress", e => {
        if (e.key == "Enter") {
            searchTable();
        }
    });

    // event listener for selection, to redraw the table when the dropdown is changed
    document.getElementById("supplierSelect").addEventListener("change", e => {
        sessionStorage.setItem(-3, document.getElementById("supplierSelect").value);
        $("#tableContainer").scrollTop(0);
        searchTable();
    });

});

/**
 * This function sets the filter string, deletes the current table,
 * and calls for the table to be redrawn.
 */
function searchTable() {
    sessionStorage.setItem(-2, $("#supplierSelect").val());
    $("#searchControls").submit();
}