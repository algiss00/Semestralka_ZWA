var xhr = new XMLHttpRequest();

function init() {
    document.getElementById("signupSub").addEventListener("click", registrUser);
}

/**
 * refistrace uzivatele
 */
function registrUser(e) {
    if (validate() === false) {
        e.preventDefault();
        return;
    }
    let user = {
        username: $('#username').val(),
        password: $('#password').val(),
        name: $('#name').val(),
        surname: $('#surname').val(),
        email: $('#email').val()
    }
    var url = "../API/user.php";
    xhr.addEventListener('load', processResponse);
    xhr.open('POST', url, true);
    xhr.send(JSON.stringify(user));
}

function processResponse(e) {
    var user = document.getElementById("username");
    var email = document.getElementById("email");
    var data = xhr.response;
    var parseData = JSON.parse(data);
    console.log("this is parse data ")
    console.log(parseData);
    if (parseData.status === false) {
        alert(parseData.message);
        user.classList.add("error");
        email.classList.add("error");
    } else {
        user.classList.remove("error");
        email.classList.remove("error");
        alert("Success");
        window.location.assign("../html/loginPage.php")
    }
}

/**
 * validace dat, ktere uzivatel poslal
 */
function validate(e) {
    const name = document.querySelector("#name");
    const surname = document.querySelector("#surname");
    const username = document.querySelector("#username");
    const password = document.querySelector("#password");
    const email = document.querySelector("#email");

    if (name.value.trim().length === 0) {
        markAsError("name", true);
    } else {
        markAsError("name", false);
    }

    if (surname.value.trim().length === 0) {
        markAsError("surname", true);
    } else {
        markAsError("surname", false);
    }

    if (username.value.trim().length === 0) {
        markAsError("username", true);
    } else {
        markAsError("username", false);
    }

    if (email.value.trim().length === 0 || !ValidateEmail(email.value)) {
        markAsError("email", true);
    } else {
        markAsError("email", false);
    }

    if (password.value.trim().length === 0) {
        markAsError("password", true);
    } else {
        markAsError("password", false);
    }
    return !(name.classList.value === "error" || surname.classList.value === "error" || password.classList.value === "error"
        || email.classList.value === "error" || username.classList.value === "error");
}

function ValidateEmail(mail) {
    if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)) {
        return (true)
    }
    alert("You have entered an invalid email address!")
    return (false)
}

function markAsError(id, add_remove) {
    var element = document.getElementById(id);
    if (element == null) {
        return;
    }
    if (add_remove) {
        element.classList.add("error");
    } else {
        element.classList.remove("error");
    }
}



