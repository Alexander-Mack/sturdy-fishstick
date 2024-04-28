$(document).ready(() => {
    document.getElementById("searchBar").addEventListener("keypress", e => {
        if (e.key == "Enter") {
            search();
        }
    });
});

/**
 * This function checks the search bar for a value and then passes the document
 * to searchSetup
 */
function search() {
    // get value in search bar
    var s = $("#searchBar").val();
    //if(s == undefined || s == "") return;
    // clear field
    $("#searchBar").val('');
    // select the most external 'valid' search
   
}

/**
 * This function cleans up the input until it reaches only valid
 * innerHTML text
 * @param {Array} input - the body tag to search as an array of children
 * @param {String} target - the search parameters provided by user
 */
function searchSetup(input, target) {
    // for each child in the html element
    for (let i = 0; i < input.length; i++) {
        // create an array from one of the html element children
        var array = $(input[i]).children();
        // if that array has any children, recurse
        if (array.length > 0) searchSetup(array, target);
        // and as long as it's not a script tag
        else if (input[i].tagName != "SCRIPT")
            processSearch(input[i].innerHTML, target);
    }
}

/**
 * This function checks each html text section for matches
 * @param {String} input - valid HTML text for searching
 * @param {String} target - the search parameters provided by user
 */
function processSearch(input, target) {
    console.log(input, target);
    // split input into array of words
    input = input.split(" ");
    // if array is more than one word
    if (input.length > 1) {
        for (let i = 0; i < input.length; i++) {
            processSearch(input[i], target);
        }
    }
    // now check for matches with levenshtein distance
}

function levenshteinFullMatrix(str1, str2) {
    const m = str1.length;
    const n = str2.length;

    const dp = new Array(m + 1).fill(null).map(() => new Array(n + 1).fill(0));

    // Initialize the first row 
    // and column of the matrix
    for (let i = 0; i <= m; i++) {
        dp[i][0] = i;
    }
    for (let j = 0; j <= n; j++) {
        dp[0][j] = j;
    }

    for (let i = 1; i <= m; i++) {
        for (let j = 1; j <= n; j++) {
            if (str1[i - 1] === str2[j - 1]) {
                dp[i][j] = dp[i - 1][j - 1];
            } else {
                dp[i][j] = 1 + Math.min(
                    // Insert
                    dp[i][j - 1],
                    Math.min(
                        // Remove
                        dp[i - 1][j],
                        // Replace
                        dp[i - 1][j - 1]
                    )
                );
            }
        }
    }
    return dp[m][n];
}