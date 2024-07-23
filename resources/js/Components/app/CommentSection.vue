<script setup>
import {
    HandThumbUpIcon,
    XMarkIcon,
    ChatBubbleLeftEllipsisIcon,
} from "@heroicons/vue/20/solid";
import InputTextArea from "../InputTextArea.vue";
import IndigoButton from "./IndigoButton.vue";
import SubText from "./SubText.vue";
import MenuOption from "./MenuOption.vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";

const authUser = usePage().props.auth.user;

const props = defineProps({
    post: Object,
    data: Object,
    parentComment: {
        type: [Object, null],
        default: null,
    },
});

const emit = defineEmits(["commentCreate", "commentDelete"]);

// handleProvider share
const editingComment = ref(null);
const newCommentText = ref("");

function commentEdit(comment) {
    // display comment edit
    editingComment.value = {
        id: comment.id,
        comment: comment.comment.replace(/<br\s*\/?>/gi, ""),
        // comment.comment  content in data CommentResource
        // </br>, <br>, <br/>  <br />
    };
}

function createComment() {
    axiosClient
        .post(route("post.comment.create", props.post), {
            comment: newCommentText.value,
            parent_id: props.parentComment?.id || null,
        })
        .then(({ data }) => {
            newCommentText.value = "";
            props.data.comments.unshift(data);
            if (props.parentComment) {
                props.parentComment.num_of_comments++;
            }
            props.post.num_of_comments++;
            emit("commentCreate", data);
        });
}

function deleteComment(commentUnique) {
    if (!window.confirm("Are you want to delete this comment")) {
        return false;
    }

    axiosClient
        .delete(route("comment.delete", commentUnique.id))
        .then(({ data }) => {
            // newCommentText.value = "";
            const commentIndex = props.data.comments.findIndex(
                (c) => c.id === commentUnique.id
            );
            props.data.comments.splice(commentIndex, 1);

            if (props.parentComment) {
                props.parentComment.num_of_comments--;
            }
            props.post.num_of_comments--;
            emit("commentDelete", comment);
        });
}

function updateComment() {
    axiosClient
        .put(
            route("comment.update", editingComment.value.id),
            editingComment.value
        )
        .then(({ data }) => {
            editingComment.value = null;
            // respone return new CommentResource($comment)
            console.log(data);
            // gán giá trị để cập nhật
            props.data.comments = props.data.comments.map((c) => {
                if (c.id === data.id) {
                    return data;
                }
                return c;
            });
        });
}

function sendCommentReaction(comment) {
    axiosClient
        .post(route("comment.reaction", comment.id), {
            reaction: "like",
        })
        .then(({ data }) => {
            comment.current_user_has_reaction = data.current_user_has_reaction;
            comment.num_of_reactions = data.num_of_reactions;
        });
    // .then((response) => {
    //     console.log(response.data);
    // });
}

function onCommentCreate(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments++;
    }
    emit("commentCreate", comment);
}

function onCommentDelete(comment) {
    if (props.parentComment) {
        props.parentComment.num_of_comments--;
    }
    emit("commentDelete", comment);
}
</script>
<template>
    <div>
        <div class="flex gap-2 mb-3">
            <a href="javascript:void(0)">
                <img
                    :src="authUser.avatar_url"
                    alt=""
                    class="w-[40px] h-[40px] rounded-full"
                />
            </a>
            <div class="flex flex-1 mb-2">
                <InputTextArea
                    v-model="newCommentText"
                    rows="1"
                    class="w-full max-h-[160px] overflow-hidden resize-none rounded-r-none"
                    placeholder="Enter your comment here"
                />
                <IndigoButton
                    @click="createComment"
                    class="rounded-l-none w-[100px]"
                    >Submit
                </IndigoButton>
            </div>
        </div>
        <div>
            <!-- Comment user -->
            <!-- comments -> Comment resource -->
            <!-- <pre>{{ comments }}</pre> -->
            <div
                class="mb-4"
                v-for="comment of data.comments"
                :key="comment.id"
            >
                <div class="flex justify-between gap-2">
                    <div class="flex gap-2">
                        <a href="javascript:void(0)">
                            <img
                                :src="comment.user.avatar_url"
                                alt=""
                                class="w-[40px] h-[40px] rounded-full"
                            />
                        </a>
                        <div>
                            <h4 class="font-bold">
                                <div class="flex flex-col">
                                    <a
                                        style="line-height: normal"
                                        href="javascript:void(0)"
                                        class="hover:underline"
                                    >
                                        {{ comment.user.name }}
                                    </a>
                                    <small class="text-xs text-gray-400">{{
                                        comment.updated_at
                                    }}</small>
                                </div>
                            </h4>
                        </div>
                    </div>
                    <MenuOption
                        :user="comment.user"
                        :post="post"
                        @edit="commentEdit(comment)"
                        @delete="deleteComment(comment)"
                    />
                </div>
                <!-- Edit Comment -->
                <div
                    v-if="editingComment && editingComment.id === comment.id"
                    class="ml-12"
                >
                    <InputTextArea
                        v-model="editingComment.comment"
                        rows="1"
                        class="w-full max-h-[160px] resize-none"
                        placeholder="Enter your comment here"
                    />
                    <div class="flex justify-between">
                        <IndigoButton @click="updateComment" class="w-[100px]">
                            Update
                        </IndigoButton>

                        <div
                            @click="editingComment = null"
                            class="flex items-center cursor-pointer text-gray-500 font-bold hover:text-gray-400"
                        >
                            <XMarkIcon class="w-4 h-4" />
                            <small>Cancel</small>
                        </div>
                    </div>
                </div>
                <!-- Subtext read more, less -->

                <SubText
                    v-else
                    :content="comment.comment"
                    content-class="ml-12"
                />
                <Disclosure>
                    <div class="ml-12 mt-1 flex gap-2">
                        <button
                            @click="sendCommentReaction(comment)"
                            class="flex items-center text-xs text-indigo-500 py-0.5 px-1 rounded-lg"
                            :class="[
                                comment.current_user_has_reaction
                                    ? 'bg-indigo-50 hover:bg-indigo-100'
                                    : 'hover:bg-indigo-50',
                            ]"
                        >
                            <HandThumbUpIcon class="w-3 h-3 mr-1" />
                            <span class="mr-2">{{
                                comment.num_of_reactions
                            }}</span>
                            {{
                                comment.current_user_has_reaction
                                    ? "unlike"
                                    : "like"
                            }}
                        </button>
                        <DisclosureButton
                            class="flex items-center text-xs text-indigo-500 hover:bg-indigo-100 rounded-lg px-1 py-0.5"
                        >
                            <ChatBubbleLeftEllipsisIcon class="w-3 h-3 mr-1" />
                            {{ comment.num_of_comments }}
                            comments
                        </DisclosureButton>
                    </div>
                    <DisclosurePanel>
                        <!-- Import component trong chinh no -->
                        <!-- reply comment post -->
                        <CommentSection
                            class="ml-12 mt-2"
                            :post="post"
                            :data="{ comments: comment.comments_sub }"
                            :parent-comment="comment"
                            @comment-create="onCommentCreate"
                            @comment-delete="onCommentDelete"
                        />
                    </DisclosurePanel>
                </Disclosure>
                <!-- slot none -->
            </div>
        </div>
    </div>
</template>
