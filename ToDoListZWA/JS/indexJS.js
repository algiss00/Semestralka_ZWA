const xhrUpdateTask = new XMLHttpRequest();
const xhrDeleteTask = new XMLHttpRequest();
const xhrDeleteCategory = new XMLHttpRequest();
const xhrUpdateCategory = new XMLHttpRequest();
let taskId;
let categoryId;
let catIdTrash;
const categories = [];
//kolik mam uakzat.. od.. do
let offset = [0, 2];

//todo pridat v dokumentaci strankovani je v JS
/**
 * inicializacni funkce, tady vsechno zacina
 *
 */
function init() {
    $(document).ready(() => {
        $.get("../API/category.php").done(async function (res) {
            //poleCatsPaging = JSON.stringify(res);
            let cats = $('#cats')
            if (res.status !== false) {
                for (let cat of res) {
                    let htmlElement = await createCategory(cat.title, cat.id, cat)
                    //console.log(htmlElement)
                    //cats.html(cats.html() + htmlElement)
                    categories.push(htmlElement);
                }
            }
            //console.log(categories)
            if (categories.length === 0) {
                $('#right').addClass("disabled");
                cats.html("No categories :) ");
                return;
            }
            if (categories.length <= 3) {
                $('#right').addClass("disabled");
                for (let j = 0; j < categories.length; j++) {
                    cats.html(cats.html() + categories[j]);
                }
                addModalListeners();
                await createMenuCat();
                return;
            }
            for (let i = offset[0]; i <= offset[1]; i++) {
                cats.html(cats.html() + categories[i])
            }
            //listeners for modal
            addModalListeners();

            await createMenuCat();
        }).fail(onFail);

        $('#newCat').click(openModal)
        $('#logoutButton').click(logout)
        $('#createCatBut').click(createCatAPI)
        $('#deadlineModal').blur(validateUpdate);
        $('#taskTitle').blur(validateUpdate);
        $('#updateTask').click(updateTask);
        $('#deleteTaskModal').click(deleteTask);
        $('#updateCategBut').click(updateCategoryModal);
        $('#left').click(showLessLeft);
        $('#right').click(showMoreRight);
    })
}

/**
 * pro strankovani vlevo
 */
function showLessLeft() {
    $('#right').removeClass("disabled");
    // 3 .. 5
    // 0 .. 2
    offset[0] = offset[0] - 3;
    offset[1] = offset[1] - 3;

    let cats = $('#cats')
    cats.html('');
    for (let i = offset[0]; i <= offset[1]; i++) {
        cats.html(cats.html() + categories[i])
    }
    addModalListeners();
    if (0 >= offset[0]) {
        $('#left').addClass("disabled");
    } else {
        $('#left').removeClass("disabled");
    }
}

/**
 * pro strankovani vpravo
 */
function showMoreRight(e) {
    $('#left').removeClass("disabled");
    // 0 .. 2
    // 3 .. 5
    // 6 .. 8
    offset[0] = offset[1] + 1;
    offset[1] = offset[0] + 2;

    let cats = $('#cats')
    cats.html('');
    for (let i = offset[0]; i <= offset[1]; i++) {
        if (categories.length - 1 < i) {
            continue;
        }
        cats.html(cats.html() + categories[i])
    }
    addModalListeners();
    if (categories.length - 1 <= offset[1]) {
        $('#right').addClass("disabled");
        $('#left').removeClass("disabled");
    } else {
        $('#right').removeClass("disabled");
    }
}

// function trashFunc(item) {
//     $(`#trash_${escapeHtml(item)}`).click(deleteCategory);
// }
//
// function catsFunc(item) {
//     $(`#catId_${escapeHtml(item)}`).click(openModalUpdateCat);
// }
//
// function arrFun(item) {
//     //console.log("ITEM " + item);
//     $(`#taskId_${escapeHtml(item)}`).click(openModalTask);
// }
/**
 * update category pres API method PUT
 */
function updateCategoryModal(e) {
    if (validateUpdateCategory(e) === false || isNaN($('#categPosition').val())) {
        e.preventDefault();
        alert("Title input is empty or position is not a number")
        return;
    }
    //console.log("CAT ID " + categoryId + " ISNAN " + isNaN($('#categPosition').val()))
    let catupdate = {
        title: $('#categTitle').val(),
        position_list: parseInt($('#categPosition').val())
    }
    var url = "../API/category.php?id=" + categoryId;
    xhrUpdateCategory.addEventListener('load', processResponseCategoryUpdate);
    xhrUpdateCategory.open('PUT', url, true);
    xhrUpdateCategory.send(JSON.stringify(catupdate));
}

/**
 * validace dat, ktere uzivatel poslal
 */
function validateUpdateCategory(e) {
    let titleCat = document.getElementById("categTitle");

    if (titleCat.value.trim().length === 0) {
        e.preventDefault();
        markAsError("categTitle", true);
    } else {
        markAsError("categTitle", false);
    }
    return !(titleCat.classList.value === "error");
}

/**
 * response pro updateCategoryModal()
 */
function processResponseCategoryUpdate() {
    var data = xhrUpdateCategory.response;
    var parseData = JSON.parse(data);
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        alert("Success");
        location.reload();
    }
}

/**
 * smazani tasku
 */
function deleteTask(e) {
    if (confirm("Delete task?")) {
        var url = "../API/task.php?id=" + taskId;
        xhrDeleteTask.addEventListener('load', processResponseDelete);
        xhrDeleteTask.open('DELETE', url, true);
        xhrDeleteTask.send();
    }
}

/**
 *response pro deleteTask()
 */
function processResponseDelete(e) {
    var data = xhrDeleteTask.response;
    var parseData = JSON.parse(data);
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        console.log(taskId)
        alert("Success");
        location.reload();
    }
}

/**
 * update tasku
 * @param e
 */
function updateTask(e) {
    console.log(taskId);
    if (validateUpdate(e) === false) {
        e.preventDefault();
        alert("Title input or Deadline input is empty")
        return;
    }
    let taskUpdate = {
        title: ($('#taskTitle').val()),
        description: ($('#descriptionModal').val()),
        deadline: encodeURIComponent($('#deadlineModal').val()),
        status: encodeURIComponent($('#statusTaskModal').val())
    }
    var url = "../API/task.php?id=" + taskId;
    xhrUpdateTask.addEventListener('load', processResponseUpdate);
    xhrUpdateTask.open('PUT', url, true);
    xhrUpdateTask.send(JSON.stringify(taskUpdate));
}

/**
 *response pro updateTask()
 */
function processResponseUpdate(e) {
    var data = xhrUpdateTask.response;
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
function validateUpdate(e) {
    let titleTask = document.getElementById("taskTitle");
    //let descriptionTask = document.getElementById("descriptionModal");
    let deadlineTask = document.getElementById("deadlineModal");

    if (titleTask.value.trim().length === 0) {
        e.preventDefault();
        markAsError("taskTitle", true);
    } else {
        markAsError("taskTitle", false);
    }

    if (deadlineTask.value.trim().length === 0) {
        e.preventDefault();
        markAsError("deadlineModal", true);
    } else {
        markAsError("deadlineModal", false);
    }
    return !(titleTask.classList.value === "error" || deadlineTask.classList.value === "error");
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

/**
 * Jako htmlspecialchars v php ale pro JS
 */
function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/**
 * Vertikalni menu vlevo obsahujici categorie
 */
function createMenuCat() {
    $.get("../API/category.php?group").done(async function (res) {
        let menu = '';
        //in for objects key value 0:asd ...
        //of for arrays
        for (const item of res) {
            let html = `<a class="teal item" id="menuLink_${escapeHtml(item[1])}" data-id="${escapeHtml(item[1])}" 
                    style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                    ${escapeHtml(item[0])}
                <div class="ui teal left pointing label">${item[2]}</div>
            </a>`
            menu += html;
        }
        $('#verticalMenu').html(menu);
        for (const item of res) {
            $(`#menuLink_${escapeHtml(item[1])}`).click(scrollToElement);
        }
    })
}

/**
 * scroll ted tam neni, ale jen oznacuje tu vybranou categorie
 */
function scrollToElement(e) {
    let catId = e.target.getAttribute("data-id")
    let id = "#" + catId;
    console.log("ID " + id);
    let el = document.getElementById(catId);
    if (el == null) {

        return;
    }
    el.addEventListener("click", function () {
        document.getElementById(catId).classList.remove("search");
    })
    // console.log(id)
    // $(document).ready(function () {
    el.classList.add("search");
    //     $('html, body').animate({
    //         scrollTop: $(id).offset().top
    //     }, 500);
    // });
}

/**
 * async funkce pres ajax create category
 */
async function createCatAPI() {
    var inputModal = document.getElementById("modalCategory");
    if (inputModal.value.trim().length === 0) {
        alert("Empty input title!");
        return;
    }
    let category = {
        title: $('#modalCategory').val(),
        position_list: 4
    }
    let newCat = await $.ajax({
        type: "POST",
        url: "../API/category.php",
        data: JSON.stringify(category),
    }).fail(onFailCategory);
    let cats = $('#cats')
    cats.html('')
    $.get("../API/category.php").done(async function (res) {
        categories.length = 0;
        for (let cat of res) {
            let htmlElement = await createCategory(cat.title, cat.id, cat)
            //cats.html(cats.html() + htmlElement)
            categories.push(htmlElement);
        }
        $('#left').addClass("disabled");
        $('#right').removeClass("disabled");
        offset = [0, 2];
        if (categories.length <= 3) {
            $('#right').addClass("disabled");
            for (let j = 0; j < categories.length; j++) {
                cats.html(cats.html() + categories[j]);
            }
            addModalListeners();
            await createMenuCat();
            return;
        }
        for (let i = offset[0]; i <= offset[1]; i++) {
            cats.html(cats.html() + categories[i])
        }
        addModalListeners();
        await createMenuCat();
    }).fail(onFail);
    inputModal.value = '';
}

/**
 * pridani listeneru kazdemu modal oknu
 */
function addModalListeners() {
    document.querySelectorAll('.buttonShowMore')
        .forEach((node) => node.addEventListener("click", openModalTask));
    document.querySelectorAll('.buttonEdit')
        .forEach((node) => node.addEventListener("click", openModalUpdateCat));
    document.querySelectorAll('.buttonTrashIcon')
        .forEach((node) => node.addEventListener("click", deleteCategory));
}

function onFailCategory(e) {
    var data = JSON.parse(e.responseText)
    console.log(data.status)
    if (data.status === false) {
        alert(data.message)
    }
    document.getElementById("modalCategory").value = '';
}

/**
 * zobrazeni do html task
 */
function createTask(task) {
    let html = `<div class="item">
    <div class="right floated content">
        <div class="ui button buttonShowMore" id="taskId_${task.Id}" data-id="${escapeHtml(task.Id)}" data-title="${escapeHtml(task.title)}" data-desc="${escapeHtml(task.description)}"
        data-deadline="${escapeHtml(task.deadline)}" data-status = ${escapeHtml(task.status)}>More</div>
    </div>
    <div class="content task-name" style="text-overflow: ellipsis; white-space: nowrap;
  overflow: hidden;">
        ${decodeURIComponent(escapeHtml(task.title))}
    </div>
    </div>`;

    return html;
}

/**
 * zobrazeni do html category
 */
async function createCategory(title, id, cat) {
    let tasks = await $.get("../API/category.php?task=all&category_id=" + id);
    let htmlEl = "";
    if (tasks.status === true) {
        for (let task of tasks.message) {
            htmlEl += createTask(task);
        }
    }
    let html = '<div class="card">' +
        `                <div class="content" id="${id}">` +
        `                    <div class="header" style="text-overflow: ellipsis; white-space: nowrap;
  overflow: hidden;">${escapeHtml(title)}
                                 </div> ` +
        `<i class="right blue pencil alternate icon buttonEdit" id="catId_${escapeHtml(cat.id)}" 
                                data-title="${escapeHtml(cat.title)}" data-id="${escapeHtml(cat.id)}"
                                data-position="${escapeHtml(cat.position_list)}"></i>
                                <i class="right red trash icon buttonTrashIcon" id="trash_${id}" data-id="${id}"></i> ` +
        ' <div class="ui middle aligned divided list">' + htmlEl +
        '                    </div>' +
        '                </div>' +
        `                <a href="createTaskPage.php?category=${escapeHtml(title)}">` +
        '                    <div class="ui bottom attached button">' +
        '                        <i class="add icon"></i>' +
        '                        Add task' +
        '                    </div>' +
        '                </a>' +
        '            </div>';
    return html;
}

/**
 * otevreni modalniho okna pro update category
 * @param e
 */
function openModalUpdateCat(e) {
    let title = e.target.getAttribute("data-title");
    let position_list = e.target.getAttribute("data-position");
    console.log("TITLE " + title);
    categoryId = e.target.getAttribute("data-id");
    $("#categTitle").val(decodeURIComponent(title));
    $("#categPosition").val(decodeURIComponent(position_list));
    $('#modalCategoryUpdate').modal('show');
}


function logout() {
    $.get("../API/logout.php").done(function (result) {
        console.log(result);
        window.location = '../html/loginPage.php'
    }).fail(onFail);
}

/**
 * delete category pres API
 */
function deleteCategory(e) {
    let catId = e.target.getAttribute("data-id");
    catIdTrash = catId;
    if (confirm("Delete category?")) {
        console.log("ID CAT:" + catId)
        var url = "../API/category.php?id=" + catId;
        xhrDeleteCategory.addEventListener('load', processResponseDeleteCategory);
        xhrDeleteCategory.open('DELETE', url, true);
        xhrDeleteCategory.send();
    }
}

/**
 * response pro deleteCategory()
 * @param e
 */
function processResponseDeleteCategory(e) {
    var data = xhrDeleteCategory.response;
    data = data.split("}")[1] + "}";
    var parseData = JSON.parse(data);
    if (parseData.status === false) {
        alert(parseData.message);
    } else {
        console.log(catIdTrash)
        alert("Success");
        location.reload();
    }
}

/**
 * modal okno pro vytvoreni category
 */
function openModal() {
    $('#modalNewCategory').modal('show');
}

/**
 * otevreni modal okna pro Task
 */
function openModalTask(e) {
    let description = e.target.getAttribute("data-desc");
    let taskTitle = e.target.getAttribute("data-title");
    let deadline = e.target.getAttribute("data-deadline");
    let statusTask = e.target.getAttribute("data-status");
    taskId = e.target.getAttribute("data-id");
    $("#taskTitle").val(decodeURIComponent(taskTitle));
    $("#descriptionModal").val(decodeURIComponent(description));
    $("#deadlineModal").val(decodeURIComponent(deadline));
    $("#statusTaskModal").val(decodeURIComponent(statusTask));
    $('#modalTask').modal('show');
}

function onFail(error) {
    console.log(error)
}