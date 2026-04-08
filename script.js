// SHOP NOW BUTTON

document.querySelector(".shop-btn")
?.addEventListener("click", function(){

    alert("Opening shop page...");

});


// OUR STORY BUTTON

document.querySelector(".ourstory")
?.addEventListener("click", function(){

    alert("Learn more about our story!");

});


// SUBSCRIBE BUTTON

document.querySelector(".sub-btn")
?.addEventListener("click", function(){

    let email =
    document.getElementById("email_input").value;

    if(email === ""){

        alert("Please enter your email first.");

    }
    else{

        alert("Thanks for subscribing!");

    }

});

// CONTACT FORM VALIDATION

document.getElementById("contactForm")
?.addEventListener("submit",
function(event){

event.preventDefault();

let inputs =
document.querySelectorAll(
".contact-form input, .contact-form textarea"
);

let filled = true;

inputs.forEach(function(input){

    if(input.value.trim() === ""){
        filled = false;
    }

});

if(filled){

    alert("Successfully submitted!");

    document.getElementById(
    "contactForm"
    ).reset();

}
else{

    alert("Please fill in all fields.");

}

});