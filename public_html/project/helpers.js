function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
}

// par36 11/4/24 - Added new, universally usable functions for JS validation
function isValidPassword(pw) {
    return pw.length >= 8;
}

function isValidUsername(username) {
    const pattern = /^[a-z0-9_-]{3,16}$/; // par36 11/4/24 - uses regex to check username
    return pattern.test(username); 
}

function isValidEmail(email) {
    const pattern = /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})*$/; 
    return pattern.test(email); // ^ par36 11/4/24 - Used from this example: https://digitalfortress.tech/js/top-15-commonly-used-regex/ 
}

function newEqualsConfirm(pw, con) {
    return pw === con; 
}