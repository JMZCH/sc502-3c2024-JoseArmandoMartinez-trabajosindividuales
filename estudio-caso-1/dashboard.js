document.addEventListener('DOMContentLoaded', function(){
    let tasks = [
        {
            id: 1,
            title: "Complete project report",
            description: "Prepare and submit the project report",
            dueDate: "2024-12-01",
            comments: []
        }
    ];

    function loadTasks(){
        const taskList = document.getElementById('task-list');
        taskList.innerHTML = '';
        tasks.forEach(task => {
            const taskCard = document.createElement('div');
            taskCard.className = 'col-md-4 mb-3';
            taskCard.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">${task.title}</h5>
                    <p class="card-text">${task.description}</p>
                    <p class="card-text"><small class="text-muted">Due: ${task.dueDate}</small></p>
                    <div class="comments-section">
                        <h6>Comments</h6>
                        <ul class="list-group" id="comments-${task.id}">
                            ${task.comments.map((comment, index) => `<li class="list-group-item d-flex justify-content-between">${comment} <button class="btn btn-sm btn-danger delete-comment" data-task-id="${task.id}" data-index="${index}">X</button></li>`).join('')}
                        </ul>
                        <div class="input-group mt-2">
                            <input type="text" class="form-control comment-input" id="comment-input-${task.id}" placeholder="Add a comment">
                            <button class="btn btn-primary add-comment" data-id="${task.id}">Add</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-secondary btn-sm edit-task" data-id="${task.id}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}">Delete</button>
                </div>
            </div>`;
            taskList.appendChild(taskCard);
        });
        attachEventListeners();
    }

    function attachEventListeners() {
        document.querySelectorAll('.edit-task').forEach(button => {
            button.addEventListener('click', handleEditTask);
        });
        document.querySelectorAll('.delete-task').forEach(button => {
            button.addEventListener('click', handleDeleteTask);
        });
        document.querySelectorAll('.add-comment').forEach(button => {
            button.addEventListener('click', handleAddComment);
        });
        document.querySelectorAll('.delete-comment').forEach(button => {
            button.addEventListener('click', handleDeleteComment);
        });
    }

    function handleEditTask(event) {
        const taskId = parseInt(event.target.dataset.id);
        const task = tasks.find(t => t.id === taskId);
        if (task) {
            document.getElementById('task-id').value = task.id;
            document.getElementById('task-title').value = task.title;
            document.getElementById('task-desc').value = task.description;
            document.getElementById('due-date').value = task.dueDate;
            const taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
            taskModal.show();
        }
    }

    function handleDeleteTask(event) {
        const taskId = parseInt(event.target.dataset.id);
        tasks = tasks.filter(task => task.id !== taskId);
        loadTasks();
    }

    function handleAddComment(event) {
        const taskId = parseInt(event.target.dataset.id);
        const inputField = document.getElementById(`comment-input-${taskId}`);
        const commentText = inputField.value.trim();
        if (commentText !== "") {
            const task = tasks.find(t => t.id === taskId);
            task.comments.push(commentText);
            inputField.value = "";
            loadTasks();
        }
    }

    function handleDeleteComment(event) {
        const taskId = parseInt(event.target.dataset.taskId);
        const commentIndex = parseInt(event.target.dataset.index);
        const task = tasks.find(t => t.id === taskId);
        task.comments.splice(commentIndex, 1);
        loadTasks();
    }

    document.getElementById('task-form').addEventListener('submit', function(e){
        e.preventDefault();
        const taskId = document.getElementById('task-id').value;
        const taskTitle = document.getElementById('task-title').value;
        const taskDesc = document.getElementById('task-desc').value;
        const dueDate = document.getElementById('due-date').value;
        
        if (taskId) {
            const taskIndex = tasks.findIndex(t => t.id == taskId);
            if (taskIndex !== -1) {
                tasks[taskIndex].title = taskTitle;
                tasks[taskIndex].description = taskDesc;
                tasks[taskIndex].dueDate = dueDate;
            }
        } else {
            const newTask = {
                id: tasks.length ? Math.max(...tasks.map(t => t.id)) + 1 : 1,
                title: taskTitle,
                description: taskDesc,
                dueDate,
                comments: []
            };
            tasks.push(newTask);
        }
        document.getElementById('task-form').reset();
        const taskModal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
        taskModal.hide();
        loadTasks();
    });

    loadTasks();
});
