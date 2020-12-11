function initLogin() {
    document.getElementById("loginBut").addEventListener("click", login);
}

/**
 * validace dat, ktere uzivatel poslal
 */
function validate() {
    var username = document.getElementById("username");
    var password = document.getElementById("password");

    if (username.value.trim().length === 0) {
        markAsError("username", true);
    } else {
        markAsError("username", false);
    }

    if (password.value.trim().length === 0) {
        markAsError("password", true);
    } else {
        markAsError("password", false);
    }
    return !(username.classList.value === "error" || password.classList.value === "error");

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

function login(e) {
    if (validate() === false) {
        e.preventDefault();
        return;
    }
    let data = $("#login_form").serializeArray();

    $.post("../API/login.php", data)
        .done(getResult)
        .fail(getFail)
}

function getResult(res) {
    window.location = "./index.php"
}

function getFail(error) {
    document.getElementById("username").value = "";
    document.getElementById("password").value = "";
    alert(error.responseJSON.message)
}