<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    post: {
        type: Object,
    },
    showTime: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="flex items-center gap-2">
        <Link :href="route('profile', post.user.id)">
            <img
                :src="post.user.avatar_url"
                alt=""
                class="w-[40px] h-[40px] rounded-full"
            />
        </Link>
        <div>
            <h4 class="font-bold">
                <template v-if="post.group">
                    <div class="flex flex-col">
                        <Link
                            :href="route('profile', post.user.id)"
                            class="hover:underline"
                        >
                            {{ post.group.name }}
                        </Link>
                        <div class="flex items-center justify-items-center">
                            <Link :href="route('profile', post.user.id)">
                                <small
                                    class="hover:underline leading-7 text-gray-500"
                                    >{{ post.user.name }}</small
                                >
                            </Link>
                            <small class="leading-7 pl-2 text-gray-400">{{
                                post.created_at
                            }}</small>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="flex flex-col">
                        <Link
                            :href="route('profile', post.user.id)"
                            class="hover:underline"
                        >
                            {{ post.user.name }}
                        </Link>
                        <small
                            v-if="showTime"
                            class="leading-7 text-gray-400"
                            >{{ post.updated_at }}</small
                        >
                    </div>
                </template>
            </h4>
        </div>
    </div>
</template>
<style lang=""></style>
