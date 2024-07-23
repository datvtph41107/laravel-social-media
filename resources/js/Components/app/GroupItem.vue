<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    group: Object,
});
</script>
<template>
    <Link
        :href="route('group.profile', group.slug)"
        class="flex items-start gap-3 cursor-pointer hover:bg-gray-100 p-[16px]"
    >
        <!-- <pre>{{ group }}</pre> -->
        <img
            class="w-[32px] h-[32px] rounded-full"
            :src="group.thumbnail_url"
            alt=""
        />
        <div class="flex-1">
            <div class="flex justify-between">
                <h3 class="font-black text-lg">{{ group.name }}</h3>
                {{
                    group.status === "approved"
                        ? group.role === "admin"
                            ? group.role
                            : ""
                        : "not approved"
                }}
            </div>
            <div class="text-xs text-gray-500">{{ group.description }}</div>
        </div>
    </Link>
</template>
