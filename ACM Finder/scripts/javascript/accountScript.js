/**
 * This function sends the user to a new page with information 
 * about the table row they selected
 * @param {*} r - The table row to be sent to a new page
 */
function tableSelection(r, row) {
    var url = 'userListing.php';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="text" name="panel_id" value="' + r + '" />' +
        '</form>');
    $('#' + r).append(form);
    form.submit();
}

/**
 * This function sends the user to a new page with information 
 * about the table row they selected
 * @param {*} r - The table row to be sent to a new page
 */
function requestSelection(r, row){
    var url = 'userListing.php';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="text" name="request_id" value="' + r + '" />' +
        '</form>');
    $('#' + r).append(form);
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

/**
 * This function pops up the delete confirmation, and keeps the confirm button
 * deactivated for 3 seconds while counting down to prevent misclicks
 */
function confirmDelete() {
    $("#backdrop").css("display", "block");
    $("#confirmDelete").css("display", "grid");
    $("#confirmDeleteButton").prop("disabled", true);
    time = 3;
    $("#confirmDeleteButton").text("Please wait... " + time + "s");
    time--;
    var deleteInterval = setInterval(() => {
        $("#confirmDeleteButton").text("Please wait... " + time + "s");
        time--;
    }, 1000)
    setTimeout(() => {
        clearInterval(deleteInterval);
        $("#confirmDeleteButton").text("Yes, I am sure");
        $("#confirmDeleteButton").prop("disabled", false);
    }, 3000)

}

/**
 * This function makes the delete popup invisible and removes dimming
 */
function exitDelete() {
    $("#confirmDelete").fadeOut();
    $("#backdrop").fadeOut();
}

/**
 * This function makes the change popup appear, and decides which inputs to show
 * @param {string} field The input to change
 */
function change(field, value) {
    $('#confirmDelete').css("display", "grid");
    $("#backdrop").css("display", "block");
    var provArr = [
        ["AB", "Alberta"],
        ["BC", "British Columbia"],
        ["MB", "Manitoba"],
        ["NB", "New Brunswick"],
        ["NL", "Newfoundland and Labrador"],
        ["NT", "Northwest Territories"],
        ["NS", "Nova Scotia"],
        ["NU", "Nunavut"],
        ["ON", "Ontario"],
        ["PE", "Prince Edward Island"],
        ["QC", "Québec"],
        ["SK", "Saskatchewan"],
        ["YK", "Yukon"]
    ];
    switch (field) {
        case "Name":
        case "Email":
        case "Street":
        case "City":
        case "Province":
            provArr.forEach((e)=>{
                if(e[0] == value){
                    value = e[1];
                }
            });
        case "Postal Code":
        case "Phone Number":
            displaySingleInput(field, value);
            break;
        case "Password":
            displayTripleInputs(field, value);
            break;
        default:
            exitDelete();
    }
}

function clearAndExit() {
    setTimeout(() => {
        $("#currentSpan, .newLabel, .newInput").remove();
    }, 500);
    $("#confirmDelete").fadeOut();
    $("#backdrop").fadeOut();
}

function displaySingleInput(field, value) {
    var form = document.getElementById("changeForm");
    var span = document.createElement("span");
    span.setAttribute("id", "currentSpan");
    span.innerHTML = "Your current " + field.toLowerCase() + " is:<br> " + value;
    var label = document.createElement("label");
    label.setAttribute("class", "newLabel");
    label.setAttribute("for", field);
    label.innerHTML = "<br>New " + field + ": <br>";
    var [inputType, placeholder, autocomplete] = setInputType(field);
    console.log([inputType], [placeholder, autocomplete]);
    if (inputType != 'select') {
        var input = document.createElement("input");
        input.setAttribute("class", "newInput");
        input.setAttribute("type", [inputType]);
        input.setAttribute("placeholder", [placeholder]);
        input.setAttribute("autocomplete", [autocomplete]);
        input.setAttribute("required", "true");
    } else {
        var input = populateSelect();
    }
    input.setAttribute("name", field);
    form.prepend(input);
    form.prepend(label);
    form.prepend(span);
    
    
}

function displayTripleInputs(field, value){
    // create 3 inputs
    // old password
    // new password
    // password confirm
    var [inputType, autocomplete, placeholder] = setInputType(field);
    var form = document.getElementById("changeForm");
    var oldPassword = document.createElement("input");
    var newPassword = document.createElement("input");
    var confirmPassword = document.createElement("input");
    var temp = [confirmPassword, newPassword, oldPassword];
    var tempName = ["Confirm new", "New", "Old"];
    var count = 0;
    temp.forEach(input=>{
        // create label
        var label = document.createElement("label");
        label.setAttribute("for", tempName[count]);
        label.setAttribute("class", "newLabel");
        label.innerHTML = tempName[count] + " password:" ;
        // create input field
        input.setAttribute("class", "newInput");
        input.setAttribute("name", tempName[count]);
        input.setAttribute("type", [inputType]);
        input.setAttribute("required", "true");
        form.prepend(input);
        form.prepend(label);
        count++;
    });

}

function setInputType(field) {
    var type, placeholder, autocomplete;
    switch (field) {
        case "Name":
            type = "text";
            placeholder = "Username";
            autocomplete = "nickname";
            break;
        case "Street":
            autocomplete = "street-address";
            placeholder = "Street";
            type = "text";
            break;
        case "City":
            placeholder = "City";
            autocomplete = "address-level2";
            type = "text";
            break;
        case "Postal Code":
            autocomplete = "postal-code";
            placeholder = "Postal Code";
            type = "text";
            break;
        case "Email":
            placeholder = "Email";
            autocomplete = "email";
            type = "email";
            break;
        case "Province":
            type = "select";
            placeholder = "";
            autocomplete = "";
            break;
        case "Phone Number":
            type = "tel";
            placeholder = "###-###-####";
            autocomplete = "tel-national";
            break;
        case "Password":
            type = "password";
            placeholder = "New Password";
            autocomplete = "new-password";
            break;
        default:
            "text";
    }
    console.log(type, placeholder, autocomplete);
    return [type, placeholder, autocomplete];
}

function populateSelect() {
    /*
    <select id="provinceReg" name="provinceReg"
    required>
    <option selected value="" disabled> Please choose one
    </default>
    <option value="AB">Alberta</option>
    <option value="BC">British Columbia </option>
    <option value="MB">Manitoba</option>
    <option value="NB">New Brunswick</option>
    <option value="NL">Newfoundland and Labrador </option>
    <option value="NT">Northwest Territories </option>
    <option value="NS">Nova Scotia</option>
    <option value="NU">Nunavut</option>
    <option value="ON">Ontario</option>
    <option value="PE">Prince Edward Island </option>
    <option value="QC">Québec</option>
    <option value="SK">Saskatchewan</option>
    <option value="YK">Yukon</option>
    </select>
    */
    var select = document.createElement("select");
    select.setAttribute("class", "newInput");
    select.setAttribute("name", "newProvince");
    select.setAttribute("required", "true");
    var defaultOption = document.createElement("option");
    defaultOption.setAttribute("selected", "true");
    defaultOption.setAttribute("value", "");
    defaultOption.setAttribute("disabled", "true");
    defaultOption.innerHTML = "Please choose one";
    select.appendChild(defaultOption);
    var provArr = [
        ["AB", "Alberta"],
        ["BC", "British Columbia"],
        ["MB", "Manitoba"],
        ["NB", "New Brunswick"],
        ["NL", "Newfoundland and Labrador"],
        ["NT", "Northwest Territories"],
        ["NS", "Nova Scotia"],
        ["NU", "Nunavut"],
        ["ON", "Ontario"],
        ["PE", "Prince Edward Island"],
        ["QC", "Québec"],
        ["SK", "Saskatchewan"],
        ["YK", "Yukon"]
    ];
    provArr.forEach((e) => {
        var option = document.createElement("option");
        option.setAttribute("value", e[0]); // AB
        option.innerHTML = e[1]; // Alberta
        select.appendChild(option);
    });
    return select;
}