<template>
    <AuthenticatedLayout>
        <div v-if="show" class=" bg-gray-600 bg-opacity-6 flex  justify-center items-center">
            <div class="bg-white p-6 rounded shadow-25 w-full max-w-lg">
                <h2 class="text-xl font-bold mb-4">Add New Task</h2>

                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label for="title" class="block text-gray-700">Title</label>
                        <input
                            id="title"
                            v-model="task.title"
                            type="text"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Описание</label>
                        <textarea
                            id="description"
                            v-model="task.description"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            rows="4"
                            required
                        ></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="start_date" class="block text-gray-700">Дата Начало</label>
                        <input
                            id="start_date"
                            v-model="task.start_date"
                            type="date"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <label for="end_date" class="block text-gray-700">Дата окончания</label>
                        <input
                            id="end_date"
                            v-model="task.end_date"
                            type="date"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        />
                    </div>

                    <div class="mb-4">
                        <label for="priority" class="block text-gray-700">Приоритет</label>
                        <select
                            id="priority"
                            v-model="task.priority"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700">Статус</label>
                        <select
                            id="status"
                            v-model="task.status"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        >
                            <option value="new">New</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="assigned_user" class="block text-gray-700">Назначить</label>
                        <select
                            id="assigned_user"
                            v-model="task.user_id"
                            class="mt-1 block w-full border border-gray-300 rounded p-2"
                            required
                        >
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import { defineProps } from 'vue';
import axios from 'axios';

const props = defineProps({
    users: {
        type: Array,
        default: () => []
    }
});

const show = ref(true);
const task = ref({
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    priority: '',
    status: '',
    users_id: ''
});

const successMessage = ref('');
const errorMessage = ref('');

const submitForm = async () => {

    try {
        const response = await axios.post('http://127.0.0.1:8000/tasks', task.value);
        successMessage.value = 'Task added successfully!';
        task.value = {
            title: '',
            description: '',
            start_date: '',
            end_date: '',
            priority: '',
            status: '',
            users_id: ''
        };
        console.log(response.data);
    } catch (error) {
        console.error('An error occurred:', error);
        errorMessage.value = 'Failed to add the task. Please try again.';
    }
};
</script>

<style scoped>

</style>
