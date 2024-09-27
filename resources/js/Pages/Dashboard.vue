<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, RouterLink } from 'vue-router';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';


const tasks = ref([]);
const search = ref('');
const selectedUser = ref('');
const selectedPriority = ref('');
const selectedStatus = ref('');
const notifications = ref([]);
const showCreateTaskModal = ref(false);
const showEditTaskModal = ref(false);
const taskForEdit = ref({});


const fetchTasks = async () => {
    try {
        const response = await axios.get('/api/tasks', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        tasks.value = response.data;
    } catch (error) {
        console.error('Ошибка при получении задач:', error.response.data);
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

const editTask = (task) => {
    console.log('Edit task', task);
    taskForEdit.value = { ...task }; // Копируем задачу для редактирования
    showEditTaskModal.value = true; // Показываем модал для редактирования
};

const updateTask = async () => {
    try {
        const response = await axios.put(`/api/tasks/${taskForEdit.value.id}`, taskForEdit.value);
        console.log('Task updated:', response.data);
        closeEditTask(); // Закрываем модал
        await fetchTasks(); // Обновляем список задач
    } catch (error) {
        console.error('Ошибка при обновлении задачи:', error);
    }
};


const deleteTask = async (taskId) => {
    if (!confirm("НЕ СМЕЙ УДАЛЯТЬ")) return;
    try {
        await axios.delete(`/api/tasks/${taskId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        await fetchTasks();
    } catch (error) {
        console.error('Ошибка при удалении задачи:', error.response.data);
    }
};
onMounted(async () => {
    await fetchTasks();
});
</script>

<template>
    <Head title="AdminPanel" />

    <AuthenticatedLayout>
        <div class="container mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Task Manager</h1>

<!--                <Link :href="route('task.create')"-->
<!--                      class="py-2 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">-->


            </div>

            <div class="mb-4">
                <input v-model="search" type="text" placeholder="Поиск задачи..." class="p-2 border border-gray-300 rounded w-full" />
            </div>

            <div class="mb-4">
                <label for="userFilter">Пользователь:</label>
                <input v-model="selectedUser" type="text" id="userFilter" placeholder="Введите ID пользователя" class="p-2 border border-gray-300 rounded w-full" />
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
                            <!--                            <router-link :to="{ name: 'edit_url', params: { id: task.id } }" class="bg-blue-500 text-white px-2 py-1 rounded ml-2">-->
                            <!--                                Edit-->
                            <!--                            </router-link>-->
                            <button @click="deleteTask(task.id)" class="bg-red-500 text-white px-2 py-1 rounded ml-2">Delete</button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <!--                <div v-if="notifications.length">-->
                <!--                    <h3>Notifications:</h3>-->
                <!--                    <ul>-->
                <!--                        <li v-for="notification in notifications" :key="notification.id">-->
                <!--                            You have a new task: {{ notification.data.task_title }}-->
                <!--                        </li>-->
                <!--                    </ul>-->
                <!--                </div>-->
            </div>

            <CreateTaskModal v-if="showCreateTaskModal" @close="showCreateTaskModal = false" />
        </div>
    </AuthenticatedLayout>
</template>

