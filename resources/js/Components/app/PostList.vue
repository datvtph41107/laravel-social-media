<script setup>
import { usePage } from "@inertiajs/vue3";
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import PostPreviewModal from "@/Components/app/PostPreviewModal.vue";
import { ref, onMounted } from "vue";
import axiosClient from "@/axiosClient.js";
import { onUpdated } from "vue";
import { watch } from "vue";

const page = usePage();
// console.log(page);
// page.props sẽ là các phần tử ở handleRequesInteria và posts bên homecontroller
const authUser = usePage().props.auth.user;
const showEditModal = ref(false);
const showPreviewModal = ref(false);
const editPost = ref({});
const postPreviewModal = ref({});
const loadMoreIntersect = ref(null);

const allPosts = ref({
    data: [],
    next: null,
});

const props = defineProps({
    posts: Array,
});

watch(
    () => page.props.posts,
    () => {
        if (page.props.posts) {
            allPosts.value = {
                data: page.props.posts.data,
                next: page.props.posts.links.next,
            };
        }
    },
    { deep: true, immediate: true }
);

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openPostPreviewModal(post, index) {
    postPreviewModal.value = {
        post,
        index,
    };
    showPreviewModal.value = true;
}
// Khi edit se reset du lieu moi khi da duoc tai len nhung chua submit de luu edit
function onModalHide() {
    editPost.value = {
        id: null,
        body: "",
        user: authUser,
    };
}

function loadMore() {
    // Không đủ 10 bài posts sẽ không load next return null
    // console.log(allPosts.value);
    if (!allPosts.value.next) {
        return;
    }

    axiosClient.get(allPosts.value.next).then(({ data }) => {
        allPosts.value.data = [...allPosts.value.data, ...data.data];
        console.log(allPosts.value.data);
        allPosts.value.next = data.links.next;
    });
}

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => entry.isIntersecting && loadMore()),
        {
            rootMargin: "-250px 0px 0px 0px",
        }
    );
    // quan sat element
    observer.observe(loadMoreIntersect.value);
});
</script>
<template>
    <div>
        <!-- su kien emit @editClick -->
        <PostItem
            v-for="post of allPosts.data"
            :key="post.id"
            :post="post"
            @editClick="openEditModal"
            @attachmentClick="openPostPreviewModal"
        />

        <!-- Load post -->
        <div ref="loadMoreIntersect"></div>
        <!-- <PostItem :post="post2" /> -->
        <PostModal
            :post="editPost"
            v-model="showEditModal"
            @hide="onModalHide"
        />
        <PostPreviewModal
            :attachments="postPreviewModal.post?.attachments || []"
            v-model:index="postPreviewModal.index"
            v-model="showPreviewModal"
        />
    </div>
</template>
