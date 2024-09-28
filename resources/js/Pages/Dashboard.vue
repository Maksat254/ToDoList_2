<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const tasks = ref([]);
const search = ref('');
const selectedUser = ref('');
const selectedPriority = ref('');
const selectedStatus = ref('');
const showCreateTaskModal = ref(false);
const showEditTaskModal = ref(false);
const taskForEdit = ref(null);
const newTask = ref({
    title: '',
    description: '',
    priority: '',
    status: '',
    start_date: '',
    end_date: '',
});

const fetchTasks = async () => {
    try {
        const response = await axios.get('/tasks');
        tasks.value = response.data;
    } catch (error) {
        console.error('Ошибка при получении задач:', error.response?.data || error.message);
    }
};

const filteredTasks = computed(() => {
    return tasks.value.filter(task => {
        const matchesSearch = task.title.toLowerCase().includes(search.value.toLowerCase()) ||
            task.description.toLowerCase().includes(search.value.toLowerCase());
        const matchesUser = !selectedUser.value || task.user_id == selectedUser.value;
        const matchesPriority = !selectedPriority.value || task.priority === selectedPriority.value;
        const matchesStatus = !selectedStatus.value || task.status === selectedStatus.value;

        return matchesSearch && matchesUser && matchesPriority && matchesStatus;
    });
});

const createTask = async () => {
    try {
        const taskData = {
            title: newTask.value.title,
            description: newTask.value.description,
            priority: newTask.value.priority,
            status: newTask.value.status,
            start_date: newTask.value.start_date,
            end_date: newTask.value.end_date,
        };

        const response = await axios.post('/tasks', taskData);
        tasks.value.push(response.data);
        showCreateTaskModal.value = false;
        resetNewTask();
    } catch (error) {
        console.error('Ошибка при создании задачи:', error.response?.data || error.message);
    }
};

const editTask = (task) => {
    taskForEdit.value = { ...task };
    showEditTaskModal.value = true;
};

const updateTask = async () => {
    try {
        const taskData = {
            title: taskForEdit.value.title,
            description: taskForEdit.value.description,
            priority: taskForEdit.value.priority,
            status: taskForEdit.value.status,
            start_date: taskForEdit.value.start_date,
            end_date: taskForEdit.value.end_date,
        };

        const response = await axios.put(`/tasks/${taskForEdit.value.id}`, taskData);
        console.log('Задача обновлена:', response.data);
        await fetchTasks();
        showEditTaskModal.value = false;
    } catch (error) {
        console.error('Ошибка при обновлении задачи:', error.response?.data || error.message);
    }
};

const deleteTask = async (taskId) => {
    if (!confirm("Вы уверены?")) return;
    try {
        await axios.delete(`/tasks/${taskId}`);
        await fetchTasks();
    } catch (error) {
        console.error( error.response?.data || error.message);
    }
};


const resetNewTask = () => {
    newTask.value = {
        title: '',
        description: '',
        priority: '',
        status: '',
        start_date: '',
        end_date: '',
    };
};

onMounted(async () => {
    await fetchTasks();
});
</script>

<template>
    <Head title="Task Manager" />
    <AuthenticatedLayout>
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Task Manager</h1>
                <button @click="showCreateTaskModal = true" class="bg-green-500 text-white px-2 py-1 rounded">Добавить задачу</button>
            </div>

            <!-- Фильтры -->
            <div class="mb-4">
                <input v-model="search" type="text" placeholder="Поиск задачи..." class="p-2 border border-gray-300 rounded w-full" />
            </div>

            <div class="mb-4">
                <label for="priorityFilter">Приоритет:</label>
                <select v-model="selectedPriority" id="priorityFilter" class="p-2 border border-gray-300 rounded w-full">
                    <option value="">Все</option>
                    <option value="low">Низкий</option>
                    <option value="medium">Средний</option>
                    <option value="high">Высокий</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="statusFilter">Статус:</label>
                <select v-model="selectedStatus" id="statusFilter" class="p-2 border border-gray-300 rounded w-full">
                    <option value="">Все</option>
                    <option value="new">Новый</option>
                    <option value="in_progress">В процессе</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>

            <!-- Таблица задач -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 rounded">
                    <thead>
                    <tr class="border-b border-gray-300">
                        <th class="p-2 text-left">Заголовок</th>
                        <th class="p-2 text-left">Описание</th>
                        <th class="p-2 text-left">Приоритет</th>
                        <th class="p-2 text-left">Статус</th>
                        <th class="p-2 text-left">Срок</th>
                        <th class="p-2 text-left">Пользователь</th>
                        <th class="p-2 text-left">Действие</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="task in filteredTasks" :key="task.id" class="border-b border-gray-200">
                        <td class="p-2">{{ task.title }}</td>
                        <td class="p-2">{{ task.description }}</td>
                        <td class="p-2">{{ task.priority }}</td>
                        <td class="p-2">{{ task.status }}</td>
                        <td class="p-2">{{ task.end_date }}</td>
                        <td class="p-2">{{ task.user_id }}</td>
                        <td class="p-2">
                            <button @click="editTask(task)" class="bg-blue-500 text-white px-2 py-1 rounded">Редактировать</button>
                            <button @click="deleteTask(task.id)" class="bg-red-500 text-white px-2 py-1 rounded">Удалить</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="showCreateTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-1/2 max-h-[80vh] overflow-y-auto">
                    <h2 class="text-xl font-bold mb-4">Создать задачу</h2>
                    <form @submit.prevent="createTask">
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700">Заголовок</label>
                            <input v-model="newTask.title" id="title" type="text" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Описание</label>
                            <textarea v-model="newTask.description" id="description" class="p-2 border border-gray-300 rounded w-full" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="priority" class="block text-gray-700">Приоритет</label>
                            <select v-model="newTask.priority" id="priority" class="p-2 border border-gray-300 rounded w-full" required>
                                <option value="low">Низкий</option>
                                <option value="medium">Средний</option>
                                <option value="high">Высокий</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-gray-700">Статус</label>
                            <select v-model="newTask.status" id="status" class="p-2 border border-gray-300 rounded w-full" required>
                                <option value="new">Новый</option>
                                <option value="in_progress">В процессе</option>
                                <option value="completed">Завершено</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="start_date" class="block text-gray-700">Дата начала</label>
                            <input v-model="newTask.start_date" id="start_date" type="date" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="mb-4">
                            <label for="end_date" class="block text-gray-700">Дата завершения</label>
                            <input v-model="newTask.end_date" id="end_date" type="date" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showCreateTaskModal = false" class="bg-gray-500 text-white px-2 py-1 rounded mr-2">Отмена</button>
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Создать</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showEditTaskModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                <div class="bg-white p-6 rounded shadow-lg w-1/2 max-h-[80vh] overflow-y-auto">
                    <h2 class="text-xl font-bold mb-4">Редактировать задачу</h2>
                    <form @submit.prevent="updateTask">
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700">Заголовок</label>
                            <input v-model="taskForEdit.title" id="title" type="text" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Описание</label>
                            <textarea v-model="taskForEdit.description" id="description" class="p-2 border border-gray-300 rounded w-full" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="priority" class="block text-gray-700">Приоритет</label>
                            <select v-model="taskForEdit.priority" id="priority" class="p-2 border border-gray-300 rounded w-full" required>
                                <option value="low">Низкий</option>
                                <option value="medium">Средний</option>
                                <option value="high">Высокий</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-gray-700">Статус</label>
                            <select v-model="taskForEdit.status" id="status" class="p-2 border border-gray-300 rounded w-full" required>
                                <option value="new">Новый</option>
                                <option value="in_progress">В процессе</option>
                                <option value="completed">Завершено</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="start_date" class="block text-gray-700">Дата начала</label>
                            <input v-model="taskForEdit.start_date" id="start_date" type="date" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="mb-4">
                            <label for="end_date" class="block text-gray-700">Дата завершения</label>
                            <input v-model="taskForEdit.end_date" id="end_date" type="date" class="p-2 border border-gray-300 rounded w-full" required />
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="showEditTaskModal = false" class="bg-gray-500 text-white px-2 py-1 rounded mr-2">Отмена</button>
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
