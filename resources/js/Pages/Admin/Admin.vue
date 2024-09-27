<template>
    <div class="container mt-5">
        <h1 class="text-center">Админ панель</h1>

        <div class="mb-3">
            <button class="btn btn-primary" @click="openCreateModal">Добавить задачу</button>
        </div>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Приоритет</th>
                <th>Статус</th>
                <th>Срок</th>
                <th>Назначен</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="task in tasks" :key="task.id">
                <td>{{ task.id }}</td>
                <td>{{ task.title }}</td>
                <td>{{ task.description }}</td>
                <td>{{ task.priority }}</td>
                <td>{{ task.status }}</td>
                <td>{{ task.end_date }}</td>
                <td>{{ getUserName(task.user_id) }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" @click="openEditModal(task)">Редактировать</button>
                    <button class="btn btn-danger btn-sm" @click="deleteTask(task.id)">Удалить</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="modal" tabindex="-1" :class="{ show: showModal }">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ isEditing ? 'Редактировать задачу' : 'Добавить задачу' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="isEditing ? updateTask() : createTask()">
                            <div class="mb-3">
                                <label for="title" class="form-label">Название</label>
                                <input type="text" v-model="taskData.title" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Описание</label>
                                <textarea v-model="taskData.description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Дата начала</label>
                                <input type="date" v-model="taskData.start_date" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Дата окончания</label>
                                <input type="date" v-model="taskData.end_date" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Приоритет</label>
                                <select v-model="taskData.priority" class="form-select" required>
                                    <option value="low">Низкий</option>
                                    <option value="medium">Средний</option>
                                    <option value="high">Высокий</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Статус</label>
                                <select v-model="taskData.status" class="form-select" required>
                                    <option value="new">В ожидании</option>
                                    <option value="in_progress">В процессе</option>
                                    <option value="completed">Завершено</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="assigned_user" class="form-label">Назначить пользователя</label>
                                <select v-model="taskData.user_id" class="form-select" required>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">{{ isEditing ? 'Сохранить изменения' : 'Добавить задачу' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

const tasks = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const taskData = ref({
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    priority: 'low',
    status: 'new',
    user_id: '',
});

const fetchTasks = () => {
    axios.get('/api/admin/tasks').then(response => {
        tasks.value = response.data.tasks;
    }).catch(error => {
        console.error('Ошибка при загрузке задач:', error.response ? error.response.data : error.message);
    });
};

const token = localStorage.getItem('token');
axios.get('/api/admin/tasks', {
    headers: {
        Authorization: `Bearer ${token}`,
    }
})
    .then(response => {
        tasks.value = response.data.tasks;
    })
    .catch(error => {
        console.error('Ошибка при загрузке задач:', error.response ? error.response.data : error.message);
    });

const getUserName = (userId) => {
    const user = props.users.find(user => user.id === userId);
    return user ? user.name : 'Не назначен';
};

const openCreateModal = () => {
    isEditing.value = false;
    taskData.value = { title: '', description: '', priority: 'low', status: 'new', user_id: '' };
    showModal.value = true;
};

const openEditModal = (task) => {
    isEditing.value = true;
    taskData.value = { ...task };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    taskData.value = { title: '', description: '', priority: 'low', status: 'new', user_id: '' };
};

const createTask = () => {
    axios.post('/api/admin/add-task', taskData.value)

        .then(response => {
            tasks.value.push(response.data.task);
            closeModal();
        })
        .catch(error => {
            console.error('Ошибка при создании задачи:', error.response ? error.response.data : error.message);
        });
};

const updateTask = () => {
    axios.put(`/api/admin/tasks/${taskData.value.id}`, taskData.value)
        .then(response => {
            const index = tasks.value.findIndex(task => task.id === taskData.value.id);
            if (index !== -1) {
                tasks.value[index] = response.data.task;
            } else {
                console.error('Задача не найдена в списке');
            }
            closeModal();
        })
        .catch(error => {
            console.error('Ошибка при обновлении задачи:', error.response ? error.response.data : error.message);
        });
};

const deleteTask = (id) => {
    axios.delete(`/api/admin/tasks/${id}`)
        .then(() => {
            tasks.value = tasks.value.filter(task => task.id !== id);
        })
        .catch(error => {
            console.error('Ошибка при удалении задачи:', error.response ? error.response.data : error.message);
        });
};

onMounted(() => {
    fetchTasks();
});
</script>

<style scoped>
.container {
    margin-top: 50px;
}
.table {
    margin-top: 20px;
}
.modal {
    display: none;
}
.modal.show {
    display: block;
}
</style>
