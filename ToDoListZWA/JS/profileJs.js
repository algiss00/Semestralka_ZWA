const xhrCurrUser = new XMLHttpRequest();
const xhrUpdateProf = new XMLHttpRequest();
const xhrDelete = new XMLHttpRequest();
const xhrChangePass = new XMLHttpRequest();

function initProfile() {
    currentUser();
    document.getElementById('logoutBut').addEventListener("click", logout)
    document.getElementById('updateBut').addEventListener("click", updateProfile);
    document.getElementById('deleteProfile').addEventListener("click", deleteProfile);
    document.getElementById('newPassBut').addEventListener("click", changePass);
}

/**
 * Zmena hesla uzivatele
 */
function changePass(e) {
    var currPass = document.getElementById("currPass");
    var newPass = document.getElementById("newPass");

    if (currPass.value.trim().length === 0) {
        e.preventDefault();
        markAsError("currPass", true);
        return;
    } else {
        markAsError("currPass", false);
    }
    if (newPass.value.trim().length === 0) {
        e.preventDefault();
        markAsError("newPass", true);
        return;
    } else {
        markAsError("newPass", false);
    }

    let userPass = {
        currentPassword: $('#currPass').val(),
        newPassword: $('#newPass').val(),
    }
    var url = "../API/user.php?updateUser=&changePassword";
    xhrChangePass.addEventListener('load', changePassResponose);
    xhrChangePass.open('POST', url, true);
    xhrChangePass.send(JSON.stringify(userPass));

}

/**
 * response pro changePass()
 */
function changePassResponose() {
    var data = xhrChangePass.response;
    var parseData = JSON.parse(data);
    console.log(parseData)
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        alert("Success");
        location.reload();
    }
}

/**
 * get Current user data
 */
function currentUser() {
    var url = "../API/user.php";
    xhrCurrUser.addEventListener('load', currentUserResponse);
    xhrCurrUser.open('GET', url, true);
    xhrCurrUser.send();
}

/**
 * zobrazeni do inputu data current usera
 */
function currentUserResponse(e) {
    var surname = document.getElementById("surname");
    var username = document.getElementById("username");
    var email = document.getElementById("email");
    var name = document.getElementById("name");
    var data = xhrCurrUser.response;
    var parseData = JSON.parse(data);
    name.value = parseData.name;
    surname.value = parseData.surname;
    username.value = parseData.username;
    email.value = parseData.email;
}

function deleteProfile(e) {
    if (confirm("Delete profile?")) {
        var url = "../API/user.php?deleteUser=";
        xhrDelete.addEventListener('load', deleteProcess);
        xhrDelete.open('POST', url, true);
        xhrDelete.send();
    }
}

function deleteProcess() {
    var data = xhrDelete.response;
    var parseData = JSON.parse(data);
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        alert("Success");
        location.reload();
    }
}

function updateProfile(e) {
    if (validate() === false) {
        e.preventDefault();
        return;
    }
    let user = {
        username: $('#username').val(),
        name: $('#name').val(),
        surname: $('#surname').val(),
        email: $('#email').val()
    }
    var url = "../API/user.php?updateUser=";
    xhrUpdateProf.addEventListener('load', processResponse);
    xhrUpdateProf.open('POST', url, true);
    xhrUpdateProf.send(JSON.stringify(user));
}

function processResponse() {
    var data = xhrUpdateProf.response;
    var parseData = JSON.parse(data);
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        alert("Success");
        location.reload();
    }
}

/**
 * validace dat, ktere uzivatel poslal
 */
function validate() {
    var name = document.getElementById("name");
    var surname = document.getElementById("surname");
    var username = document.getElementById("username");
    var email = document.getElementById("email");
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

    if (email.value.trim().length === 0) {
        markAsError("email", true);
    } else {
        markAsError("email", false);
    }

    return !(name.classList.value === "error" || surname.classList.value === "error" || username.classList.value === "error"
        || email.classList.value === "error");

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