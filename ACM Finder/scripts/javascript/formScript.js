/**
 * This function sets the attribute required to either the colour 
 * or reference input, acting similarly to radio buttons.
 * This will allow the user to place inputs in one or both fields, but
 * not neither.
 */
function checkColourRef() {
    var colourReq = document.getElementById("colourReq");
    var refReq = document.getElementById("refReq");
    // if a colour is given
    if(colourReq.value != ""){
        // remove required from reference
        refReq.removeAttribute("required");
        // return required to colour
        colourReq.setAttribute("required", "");
    }else if (refReq.value != ""){ // if a reference is given
        // remove required from colour
        colourReq.removeAttribute("required");
        // return required to reference
        refReq.setAttribute("required","");
    }else{ // if no input is given
        // add required to both inputs
        colourReq.setAttribute("required", "");
        refReq.setAttribute("required","");
    }
}