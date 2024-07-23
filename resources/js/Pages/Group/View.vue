<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InviteUserModal from "./InviteUserModal.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import GroupForm from "@/Components/app/GroupForm.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";

const imagesForm = useForm({
    thumbnail: null,
    cover: null,
});

const isCurrentUserAdmin = computed(() => props.group.role === "admin");
const isJoinedToGroup = computed(
    () => !!props.group.role && props.group.status === "approved"
);

const coverImageSrc = ref("");
const thumbnailImageSrc = ref("");
const searchKeyWord = ref("");
const showNotification = ref(true);
const showInviteUserModal = ref(false);

const authUser = usePage().props.auth.user;

// props tu GroupController
const props = defineProps({
    errors: Object,
    success: {
        type: String,
    },
    group: {
        type: Object,
    },
    posts: Object,
    users: Array,
    requests: Array,
});

const aboutForm = useForm({
    name: usePage().props.group.name,
    auto_approval: !!parseInt(usePage().props.group.auto_approval),
    about: usePage().props.group.about,
});

function updateGroup() {
    aboutForm.put(route("group.update", props.group.slug), {
        preserveScroll: true,
    });
}

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0];
    if (imagesForm.cover) {
        const reader = new FileReader();
        reader.onload = () => {
            coverImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.cover);
    }
}

function onThumnailChange(event) {
    imagesForm.thumbnail = event.target.files[0];
    if (imagesForm.thumbnail) {
        const reader = new FileReader();
        reader.onload = () => {
            thumbnailImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.thumbnail);
    }
}

function cancelCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function cancelThumbnailImage() {
    imagesForm.thumbnail = null;
    thumbnailImageSrc.value = null;
}

function submitCoverImage() {
    // truyen vao url ben route web
    imagesForm.post(route("group.updateImages", props.group.slug), {
        // tra ve to_route o updateImage ProfileController
        onSuccess: (group) => {
            showNotification.value = true;
            cancelCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 5000);
        },
    });
}

function submitThumbnailImage() {
    imagesForm.post(route("group.updateImages", props.group.slug), {
        // tra ve to_route o updateImage ProfileController
        onSuccess: (user) => {
            showNotification.value = true;
            cancelThumbnailImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 5000);
        },
    });
}

function joinToGroup() {
    const form = useForm({});
    form.post(route("group.join", props.group.slug));
}

// user bên UserListItem đã được lắng nghe emit approve
function approveUser(user) {
    const form = useForm({
        user_id: user.id,
        action: "approve",
    });
    form.post(route("group.approveRequest", props.group.slug));
}

function rejectUser(user) {
    const form = useForm({
        user_id: user.id,
        action: "reject",
    });
    form.post(route("group.approveRequest", props.group.slug));
}

function onRoleChange(user, valueRole) {
    console.log(user, valueRole);
    const form = useForm({
        user_id: user.id,
        valueRole,
    });
    form.post(route("group.changeRole", props.group.slug));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="container mx-auto">
            <!-- toast notify -codeholic- part 6 1:16:55 -->
            <div
                v-show="showNotification && success"
                class="mt-2 mb-2 py-2 px-3 font-medium text-sm text-white bg-emerald-500"
            >
                {{ success }}
            </div>

            <div
                v-if="errors.cover"
                class="mt-2 mb-2 py-2 px-3 font-medium text-sm text-white bg-red-500"
            >
                {{ errors.cover }}
            </div>
            <!-- <pre>{{ user }}</pre>
            {
            "data": {
                "id": 3,
                "name": "bop",
                "email": "votiendat11022004@gmail.com",
                "email_verified_at": "2024-03-01T02:02:47.000000Z",
                "created_at": "2024-03-01T02:00:51.000000Z",
                "updated_at": "2024-03-01T02:02:47.000000Z",
                "username": "bop",
                "cover_url": null,
                "avatar_url": null
            }
            } -->
            <div class="group relative">
                <div class="h-[300px] overflow-hidden">
                    <img
                        :src="
                            coverImageSrc ||
                            group.cover_path ||
                            'https://styles.redditmedia.com/t5_31m0v/styles/bannerBackgroundImage_5q1fwpq149261.jpg?format=pjpg&s=37acde3e558c661972f65b665c81ecba20317b84'
                        "
                        class="object-cover w-full"
                    />
                </div>
                <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
                    <button
                        v-if="!coverImageSrc"
                        class="flex items-center opacity-0 group-hover:opacity-100 bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5 mr-2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"
                            />
                        </svg>

                        Uploads Cover Image
                        <input
                            type="file"
                            class="absolute top-0 left-0 bottom-0 right-0 opacity-0"
                            @change="onCoverChange"
                        />
                    </button>

                    <div
                        v-else
                        class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100"
                    >
                        <button
                            class="flex items-center bg-gray-100 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm"
                            @click="cancelCoverImage"
                        >
                            <XMarkIcon class="h-4 w-4 text-gray-500 mr-2" />
                            Cancel
                        </button>
                        <button
                            class="flex items-center bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-sm"
                            @click="submitCoverImage"
                        >
                            <CheckIcon class="h-4 w-4 text-white-500 mr-2" />
                            Submit
                        </button>
                    </div>
                </div>
                <div class="flex">
                    <div
                        class="ml-[56px] flex items-center text-center justify-center relative group/thumbnail"
                    >
                        <img
                            :src="
                                thumbnailImageSrc ||
                                group.thumbnail_url ||
                                'https://th.bing.com/th/id/OIP.JBpgUJhTt8cI2V05-Uf53AHaG1?rs=1&pid=ImgDetMain'
                            "
                            class="-mt-[74px] w-[128px] h-[128px] rounded-full"
                        />
                        <div class="absolute top-2 right-2 bg-black25 w-full">
                            <button
                                v-if="isCurrentUserAdmin && !thumbnailImageSrc"
                                class="rounded-full opacity-0 flex items-center justify-center group-hover/thumbnail:opacity-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-8 h-8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"
                                    />
                                </svg>

                                <input
                                    type="file"
                                    class="absolute top-0 left-0 bottom-0 right-0 opacity-0"
                                    @change="onThumnailChange"
                                />
                            </button>

                            <div
                                v-else-if="isCurrentUserAdmin"
                                class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100"
                            >
                                <!-- 1.57 -->
                                <button
                                    class="flex items-center bg-gray-100 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm"
                                    @click="cancelThumbnailImage"
                                >
                                    <XMarkIcon
                                        class="h-4 w-4 text-gray-500 mr-2"
                                    />
                                    Cancel
                                </button>
                                <button
                                    class="flex items-center bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-sm"
                                    @click="submitThumbnailImage"
                                >
                                    <CheckIcon
                                        class="h-4 w-4 text-white-500 mr-2"
                                    />
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 items-center flex justify-between pl-4">
                        <h2 class="font-bold text-xl">{{ group.name }}</h2>
                        <PrimaryButton v-if="!authUser" :href="route('login')">
                            Login to join to this group
                        </PrimaryButton>

                        <PrimaryButton
                            v-if="isCurrentUserAdmin"
                            @click="showInviteUserModal = true"
                        >
                            Invite Users
                        </PrimaryButton>
                        <PrimaryButton
                            v-if="
                                authUser && !group.role && group.auto_approval
                            "
                            @click="joinToGroup"
                        >
                            Join to Group
                        </PrimaryButton>
                        <PrimaryButton
                            v-if="
                                authUser && !group.role && !group.auto_approval
                            "
                            @click="joinToGroup"
                        >
                            Request to join
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <div class="">
                <TabGroup>
                    <TabList class="pl-[200px] flex bg-gray">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>

                        <Tab
                            v-if="isJoinedToGroup"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem text="Followers" :selected="selected" />
                        </Tab>
                        <Tab
                            v-if="isCurrentUserAdmin"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem
                                text="Pending Request"
                                :selected="selected"
                            />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab
                            v-if="isCurrentUserAdmin"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem text="About" :selected="selected" />
                        </Tab>
                    </TabList>
                    <TabPanels class="mt-2">
                        <TabPanel>
                            <template v-if="posts">
                                <CreatePost :group="group" />
                                <PostList :posts="posts.data" class="flex-1" />
                            </template>
                            <div v-else class="py-8 text-center">
                                You don't have permission to view these posts.
                            </div>
                        </TabPanel>

                        <TabPanel
                            v-if="isJoinedToGroup"
                            class="bg-white p-3 shadow"
                        >
                            <div class="mb-3 grid grid-cols-2">
                                <TextInput
                                    :model-value="searchKeyWord"
                                    placeholder="Type to search user"
                                    class="mt-3"
                                />
                            </div>
                            <!-- was join group -->
                            <div class="grid grid-cols-2 gap-3">
                                <pre>{{ users }}</pre>
                                <UserListItem
                                    v-for="user of users"
                                    :key="user.id"
                                    :user="user"
                                    :disable-role-dropdown="
                                        group.user_id === user.id
                                    "
                                    :show-role-dropdown="isCurrentUserAdmin"
                                    @role-change="onRoleChange"
                                />
                            </div>
                        </TabPanel>

                        <TabPanel
                            v-if="isCurrentUserAdmin"
                            class="bg-white p-3 shadow"
                        >
                            <!-- pending user request group -->
                            <div
                                v-if="requests.length > 0"
                                class="grid grid-cols-2 gap-3"
                            >
                                <UserListItem
                                    v-for="user of requests"
                                    :key="user.id"
                                    :for-approve="true"
                                    :user="user"
                                    :show-role-dropdown="true"
                                    @approve="approveUser"
                                    @reject="rejectUser"
                                />
                            </div>
                            <div class="py-8 text-center">
                                There is no pending request
                            </div>
                        </TabPanel>

                        <TabPanel class="bg-white p-3 shadow">
                            Photos
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            <GroupForm :form="aboutForm" />
                            <PrimaryButton @click="updateGroup">
                                Submit
                            </PrimaryButton>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>

    <InviteUserModal v-model="showInviteUserModal" />
</template>
