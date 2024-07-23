<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import {
    HandThumbUpIcon,
    ChatBubbleLeftRightIcon,
} from "@heroicons/vue/20/solid";

import AccountItem from "./AccountItem.vue";
import { router } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import SubText from "./SubText.vue";
import MenuOption from "./MenuOption.vue";
import PostAttachment from "./PostAttachment.vue";
import CommentSection from "./CommentSection.vue";
import { ref, watch } from "vue";

// Phan menu_item tiktok_ui
const buttonReactions = ["like", "smile", "sad", "angry"];

const props = defineProps({
    post: Object,
});

// dinh nghia su kien cho thang cha PostList
const emit = defineEmits(["editClick", "attachmentClick"]);

function openEditModal() {
    emit("editClick", props.post);
}

function deletePost() {
    if (confirm("Are you sure to delete this post")) {
        router.delete(route("post.destroy", props.post));
    }
}

function openAttachmentPreview(index) {
    emit("attachmentClick", props.post, index);
}

function sendReaction() {
    axiosClient
        .post(route("post.reaction", props.post), {
            reaction: "like",
        })
        .then(({ data }) => {
            props.post.current_user_has_reaction =
                data.current_user_has_reaction;
            props.post.num_of_reactions = data.num_of_reactions;
        });
    // .then((response) => {
    //     console.log(response.data);
    // });
}
</script>
<template>
    <div class="border rounded p-4 mb-3">
        <div class="flex items-center justify-between mb-3">
            <AccountItem :post="post" />
            <!-- dropdown option -->
            <MenuOption
                :user="post.user"
                :post="post"
                @edit="openEditModal"
                @delete="deletePost"
            />
        </div>
        <div class="mb-3">
            <SubText :content="post.body" />
        </div>
        <div
            class="grid gap-3 mb-3"
            :class="[
                post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2',
            ]"
        >
            <PostAttachment
                :attachments="post.attachments"
                @attachmentClick="openAttachmentPreview"
            />
        </div>
        <Disclosure v-slot="{ open }">
            <div class="flex gap-2 mt-3">
                <button
                    @click="sendReaction"
                    class="flex gap-1 w-full hover:bg-gray-100 rounded-lg justify-center items-center py-2 px-4 flex-1"
                    :class="[
                        post.current_user_has_reaction
                            ? 'bg-sky-100 hover:bg-sky-200'
                            : 'bg-gray-100 hover:bg-gray-200',
                    ]"
                >
                    <!-- TIPPY POPU -->
                    <tippy
                        to="parent"
                        content-tag="div"
                        content-class="content-wrapper"
                        theme="light"
                        interactive
                    >
                        <button
                            v-for="(reaction, index) in buttonReactions"
                            :key="index"
                            :id="'post-button-' + post.id + '-' + index"
                            @click="
                                () => {
                                    console.log(index);
                                }
                            "
                        >
                            {{ reaction }}
                        </button>
                    </tippy>

                    <HandThumbUpIcon class="w-6 h-6 mr-2" />
                    <!--(HomeController) Muon co post.num_of_reactions thi phai khai bao ben controller voi Reaction:withCount -->
                    {{ post.num_of_reactions }}
                    {{ post.current_user_has_reaction ? "Unlike" : "Like" }}
                </button>
                <DisclosureButton
                    class="flex gap-1 w-full bg-gray-100 hover:bg-gray-200 rounded-lg justify-center items-center py-2 px-4 flex-1"
                >
                    <ChatBubbleLeftRightIcon class="w-6 h-6 mr-2" />
                    {{ post.num_of_comments }}
                    Comments
                </DisclosureButton>
            </div>

            <div ref="commentPannelTemp" class="h-auto">
                <DisclosurePanel class="comment-list mt-3 overflow-auto">
                    <!-- <pre>{{ props }}</pre> đây sẽ là của phần PostResoure trả về với key là props -->
                    <CommentSection
                        :post="post"
                        :data="{ comments: post.comments }"
                    />
                </DisclosurePanel>
            </div>
        </Disclosure>
    </div>
</template>
