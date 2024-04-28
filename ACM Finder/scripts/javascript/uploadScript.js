/**
 * This fucntion makes the individual price input appear when the checkbox is selected
 * @param {Element} input 
 */
function popupIndividual(input) {
    var container = $('#indvPriceUplContainer');
    if (input.checked) {
        container.fadeIn().css('display', 'block');
        document.getElementById("indvPriceUpl").setAttribute('required', '');
    } else {
        document.getElementById("indvPriceUpl").removeAttribute('required');
        container.fadeOut();
    }
}

/**
 * This function changes the text of the tooltip div depending on which label is hovered
 * @param {Element} label - The label being hovered
 * @param {*} tooltip - The tooltip whose text is changed
 */
function changeText(label, tooltip) {
    switch (label.getAttribute("for")) {
        case "supplierUpl":
            tooltip.text("The supplier name");
            break;
        case "colRefUpl":
            tooltip.text("The colour, and if possible, reference of the sheet");
            break;
        case "coreUpl":
            tooltip.text("The core material of the sheet");
            break;
        case "dimUpl":
            tooltip.text("The dimensions of the sheet, in millimeters");
            break;
        case "quantityUpl":
            tooltip.text("The number of sheets");
            break;
        case "conditionUpl":
            tooltip.text("The condition of the sheets");
            break;
        case "totalPriceUpl":
            tooltip.text("The total value of the sheets");
            break;
        case "allowIndividual":
            tooltip.text("Please check if you'd like to set a price per sheet as well");
            break;
        case "indvPriceUpl":
            tooltip.text("The individual price per sheet, should cost more in total");
            break;
        case "notesUpl":
            tooltip.text("Please share any extra information about the sheets here");
            break;
        default:
            tooltip.text("");
            break;
    }
}
// on document load
$(document).ready(() => {
    var tooltip = $('#infoPopup');
    var notes = document.getElementById("notesUpl");
    var container = $('#indvPriceUplContainer');
    if($("#allowIndividual:checked") == 1){
        container.fadeIn().css('display', 'block');
        document.getElementById("indvPriceUpl").setAttribute('required', '');
    }
    // radio event listener
    $("[name=coreUpl]").on("change", () => {
        // if other is selected, force user to input a note
        if ($("#otherUpl:checked").val()) {
            notes.setAttribute("required", "");
            notes.setCustomValidity("Make sure to describe the core material in the notes");
            notes.reportValidity();
        } else { // remove the requirement if not selected
            notes.removeAttribute("required");
            notes.setCustomValidity("");
        }
    });

    // label hover event listener
    $(".giveInfo").hover(function (e) {
        var info = this.getBoundingClientRect();
        // move tooltip, change text, and show
        changeText(this, tooltip);
        tooltip.css({ left: info.left });
        tooltip.css({
            top: info.top - tooltip.outerHeight()
                + document.documentElement.scrollTop - 5
        });
        tooltip.delay(1000).fadeIn(200).css("opacity", 100);
    }, function () { // on mouseout
        tooltip.dequeue();
        tooltip.hide().css("opacity", 0);
        tooltip.css({ top: -100 });
    });

    // submit click event listener
    $(":submit").click(() => {
        // if notes are not empty, clear custom validity
        if (notes.value != "") {
            // won't matter if other is not checked
            notes.setCustomValidity("");
        }
    });
});