<script setup>
import { ref } from "vue";
import PostModal from "./PostModal.vue";
import { usePage } from "@inertiajs/vue3";

const authUser = usePage().props.auth.user;
const showModal = ref(false);
const newPost = ref({
    id: null,
    body: "",
    user: authUser,
});

defineProps({
    group: {
        type: Object,
        default: null,
    },
});

function showCreatePostModal() {
    showModal.value = true;
}
</script>
<template>
    <div class="py-6">
        <div
            @click="showCreatePostModal"
            class="border-gray-300 rounded-md shadow-sm py-3 px-2 w-full text-gray-500 border"
        >
            Click here to create new post
        </div>
        <!-- Post modal -->
        <PostModal :post="newPost" :group="group" v-model="showModal" />
    </div>
</template>
