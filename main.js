
async function getTasks() {
    let res = await fetch('http://localhost:8000/ConnectDB/API/task.php?');
    let tasks = await res.json();

    tasks.forEach((task) => {
        document.querySelector('.post-list').innerHTML += '<div>\n' +
            '                       <div>\n' +
            '                           <h5> ${task.title}</h5>\n' +
            '                           <p>${task.description}</p>\n' +
            '                           <a href="#"> More </a>\n' +
            '                       </div>\n' +
            '                    </div>'

    })
    //console.log(tasks)
}

async function addTask(userId){
 const title = document.getElementById("title").value,
     description = document.getElementById("description").value,
     deadline = document.getElementById("deadline").value;

 let formData = new FormData();
 formData.append('title', title);
 formData.append('description', description);
 formData.append('deadline', deadline);

 const res = await fetch("http://localhost:8000/ConnectDB/API/task.php", {
     method: 'POST',
     body: formData
 });
  const data = await res.json();
  console.log(data)
}