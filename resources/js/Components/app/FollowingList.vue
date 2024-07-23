<script setup>
import TextInput from "../TextInput.vue";
import { ref } from "vue";
import UserListItem from "./UserListItem.vue";
const searchKeyword = ref("");
</script>
<template>
    <div class="px-3">
        <h2 class="text-2xl font-bold mb-4">My Followings</h2>
        <TextInput
            :model-value="searchKeyword"
            placeholder="Type to search"
            class="w-full"
        />
        <div class="py-8">
            <hr />
            <div v-if="false" class="p-3 text-gray-400 flex text-center">
                You don't gave any flowers
            </div>
            <div v-else class="h-[400px] overflow-auto"></div>
        </div>
    </div>
</template>
