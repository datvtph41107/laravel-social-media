<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    user: Object,
    forApprove: {
        type: Boolean,
        default: false,
    },
    showRoleDropdown: {
        type: Boolean,
        default: false,
    },
    disableRoleDropdown: {
        type: Boolean,
        default: false,
    },
});
// lặp của user request pending trong mảng thành phần tử với id và truyền vào emit để xác định id của user request
defineEmits(["approve", "reject", "roleChange"]);
</script>
<template>
    <!-- route profile web.php tham so {username} trong path -->
    <div
        class="flex items-center gap-3 cursor-pointer hover:bg-gray-100 p-[16px]"
    >
        <Link :href="route('profile', user.username)">
            <img
                class="w-[32px] rounded-full"
                :src="
                    user.avatar_url ||
                    'https://th.bing.com/th/id/OIP.GNMC0-PdhWQH3YsrPCeV0gHaHa?rs=1&pid=ImgDetMain'
                "
                alt=""
            />
        </Link>
        <div class="flex justify-between flex-1">
            <Link :href="route('profile', user.username)">
                <h3 class="font-black hover:underline">{{ user.name }}</h3>
            </Link>
            <div v-if="forApprove" class="flex gap-1">
                <button
                    class="text-xs w-[80px] py-2 px-4 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                    @click.prevent.stop="$emit('approve', user)"
                >
                    Approve
                </button>
                <button
                    class="text-xs w-[80px] py-2 px-4 rounded bg-red-500 hover:bg-red-600 text-white"
                    @click.prevent.stop="$emit('reject', user)"
                >
                    reject
                </button>
            </div>
            <div v-if="showRoleDropdown">
                <select
                    @change="$emit('roleChange', user, $event.target.value)"
                    class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6"
                    :disabled="disableRoleDropdown"
                >
                    <option :selected="user.role === 'admin'">admin</option>
                    <option :selected="user.role === 'user'">user</option>
                </select>
            </div>
        </div>
    </div>
</template>
