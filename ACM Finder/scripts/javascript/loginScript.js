/**
 * This function makes the login popup visible and dims the rest of 
 * the form
 */
function enterPopup() {
    $("#loginPopup").fadeIn().css("display", "grid");
    $("#backdrop").fadeIn().css("display", "flex");
}

/**
 * This function makes the login popup invisible and removes dimming
 */
function exitPopup() {
    $("#loginPopup").fadeOut();
    $("#backdrop").fadeOut();
}