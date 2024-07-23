<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import TextInput from "../TextInput.vue";
import { ref } from "vue";
import GroupModal from "@/Components/app/GroupModal.vue";

const searchKeyword = ref("");
const showNewGroupModal = ref(false);

const props = defineProps({
    groups: Array,
});

function onGroupCreate(group) {
    props.groups.unshift(group);
}
</script>
<template>
    <div class="flex gap-2 mt-4">
        <TextInput :model-value="searchKeyword" placeholder="Type to search" />
        <button
            @click="showNewGroupModal = true"
            class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded py-1 px-2 w-[120px]"
        >
            New Group
        </button>
    </div>
    <div class="py-8 h-[400px] lg:flex-1">
        <hr />
        <div v-if="false" class="text-gray-400 p-3 flex text-center">
            You are not joined to any groups
        </div>
        <div v-else class="h-[400px] overflow-auto">
            <GroupItem v-for="group of groups" :group="group" />
        </div>
    </div>

    <GroupModal v-model="showNewGroupModal" @create="onGroupCreate" />
</template>
