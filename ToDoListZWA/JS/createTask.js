var xhr = new XMLHttpRequest();

function initCreateTask() {
    document.getElementById('logoutBut').addEventListener("click", logout)
    document.getElementById('createTaskSub').addEventListener("click", createTask);
}

async function createTask(e) {
    if (validate() === false) {
        e.preventDefault();
        return;
    }
    let task = {
        title: $('#title').val(),
        deadline: $('#deadline').val(),
        status: $('#status').val(),
        category: $('#category').val(),
        description: $('#description').val()
    }
    console.log(task)

    let newTask = await $.ajax({
        type: "POST",
        url: "../API/task.php",
        data: JSON.stringify(task),
    }).fail(failTask);
    console.log(newTask)

    var category = document.getElementById("category").value;
    var url = "../API/category.php?" + "task_id=" + newTask.id + "&category=" + encodeURIComponent(category);
    xhr.addEventListener('load', processResponse);
    xhr.open('POST', url, true);
    xhr.send();
}

function failTask(e) {
    var deadline = document.getElementById("deadline");
    var data = e.responseText;
    var parseData = JSON.parse(data);
    console.log(parseData)
    if (parseData.status === false) {
        alert(parseData.message);
        deadline.classList.add("error");
    } else {
        deadline.classList.remove("error");
        alert("Success");
        window.location.assign("../html/index.php");
    }
}

function processResponse(e) {
    var categ = document.getElementById("category");
    var data = xhr.response;
    var parseData = JSON.parse(data);
    console.log("this is parse data ")
    console.log(parseData.status);
    if (parseData.status === false) {
        alert("Category is not exists");
        categ.classList.add("error");
    } else {
        categ.classList.remove("error");
        alert("Success");
        window.location.assign("../html/index.php");
    }
}

function validate() {
    var title = document.getElementById("title");
    var deadline = document.getElementById("deadline");
    var category = document.getElementById("category");

    if (title.value.trim().length === 0) {
        markAsError("title", true);
    } else {
        markAsError("title", false);
    }

    if (deadline.value.trim().length === 0) {
        markAsError("deadline", true);
    } else {
        markAsError("deadline", false);
    }

    if (category.value.trim().length === 0) {
        markAsError("category", true);
    } else {
        markAsError("category", false);
    }
    return !(category.classList.value === "error" || deadline.classList.value === "error" || category.classList.value === "error");

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