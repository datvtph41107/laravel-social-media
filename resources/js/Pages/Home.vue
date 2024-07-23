<script setup>
import FollowingList from "../Components/app/FollowingList.vue";
import GroupList from "../Components/app/GroupList.vue";
import CreatePost from "../Components/app/CreatePost.vue";
import PostList from "../Components/app/PostList.vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";

defineProps({
    posts: Object,
    groups: Object,
});
</script>

<template>
    <Head title="Social Media" />

    <AuthenticatedLayout>
        <div class="grid lg:grid-cols-12 gap-3 p-4">
            <div class="lg:col-span-3 lg:order-1 h-full overflow-auto">
                <GroupList :groups="groups" />
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-auto">
                <FollowingList />
            </div>
            <div class="lg:col-span-6 lg:order-2 h-full overflow-auto">
                <CreatePost />
                <PostList :posts="posts.data" class="flex-1" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style></style>
