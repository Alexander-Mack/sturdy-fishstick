/**
 * This function sends the user to a new page with information 
 * about the table row they selected
 * @param {*} r - The table row to be sent to a new page
 */
function tableSelection(r, row) {
    var url = 'listingSpecific.php';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="text" name="panel_id" value="' + r + '" />' +
        '</form>');
    $('#'+r).append(form);
    form.submit();

}

// when listingSpecific.html is loaded
$('listingSpecific.html').ready(() => {
    // get the stored length of the row
    var l = sessionStorage.getItem(-1);
    var data = [];
    // add the data back to the array
    for (let i = 0; i < l; i++) {
        data[i] = sessionStorage.getItem(i + 1);
    }
    if (data != 0) {
        displayData(data);
    }
});

function addToCartAll(){
    var form = $("#postTableContainer");
    var input = document.getElementById("addSelectedQTY");
    input.value = 0;
    form.submit();
}
function addToCartPartial(){
    var form = $("#postTableContainer");
    var input = document.getElementById("addSelectedQTY");
    if(input.value <= 0){
        input.setCustomValidity("Please input a valid, non-negative number.");
        input.reportValidity();
    }else{
        form.submit();
    }
    
}