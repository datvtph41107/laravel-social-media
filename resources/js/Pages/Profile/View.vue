<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { CheckIcon, XMarkIcon } from "@heroicons/vue/24/solid";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "./Partials/TabItem.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Edit from "./Edit.vue";

const imagesForm = useForm({
    avatar: null,
    cover: null,
});

const showNotification = ref(true);
const coverImageSrc = ref("");
const avatarImageSrc = ref("");
const authUser = usePage().props.auth.user;

const isMyProfile = computed(() => authUser && authUser.id === props.user.id);
const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    success: {
        type: String,
    },
    user: {
        type: Object,
    },
});

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

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0];
    if (imagesForm.avatar) {
        const reader = new FileReader();
        reader.onload = () => {
            avatarImageSrc.value = reader.result;
        };
        reader.readAsDataURL(imagesForm.avatar);
    }
}

function cancelCoverImage() {
    imagesForm.cover = null;
    coverImageSrc.value = null;
}

function cancelAvatarImage() {
    imagesForm.avatar = null;
    avatarImageSrc.value = null;
}

function submitCoverImage() {
    imagesForm.post(route("profile.updateImages"), {
        // tra ve to_route o updateImage ProfileController
        onSuccess: (user) => {
            cancelCoverImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 5000);
        },
    });
}

function submitAvatarImage() {
    imagesForm.post(route("profile.updateImages"), {
        // tra ve to_route o updateImage ProfileController
        onSuccess: (user) => {
            cancelAvatarImage();
            setTimeout(() => {
                showNotification.value = false;
            }, 5000);
        },
    });
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
                            user.cover_url ||
                            'https://styles.redditmedia.com/t5_31m0v/styles/bannerBackgroundImage_5q1fwpq149261.jpg?format=pjpg&s=37acde3e558c661972f65b665c81ecba20317b84'
                        "
                        class="object-cover w-full"
                    />
                </div>
                <div class="absolute top-2 right-2">
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

                        Update Cover Image
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
                        class="ml-[56px] flex items-center text-center justify-center relative group/avatar"
                    >
                        <img
                            :src="
                                avatarImageSrc ||
                                user.avatar_url ||
                                'https://th.bing.com/th/id/OIP.JBpgUJhTt8cI2V05-Uf53AHaG1?rs=1&pid=ImgDetMain'
                            "
                            class="-mt-[74px] w-[128px] h-[128px] rounded-full"
                        />
                        <div class="absolute top-2 right-2 bg-black25 w-full">
                            <button
                                v-if="!avatarImageSrc"
                                class="rounded-full opacity-0 flex items-center justify-center group-hover/avatar:opacity-100"
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
                                    @change="onAvatarChange"
                                />
                            </button>

                            <div
                                v-else
                                class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100"
                            >
                                <!-- 1.57 -->
                                <button
                                    class="flex items-center bg-gray-100 hover:bg-gray-100 text-gray-800 py-1 px-2 text-sm"
                                    @click="cancelAvatarImage"
                                >
                                    <XMarkIcon
                                        class="h-4 w-4 text-gray-500 mr-2"
                                    />
                                    Cancel
                                </button>
                                <button
                                    class="flex items-center bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-sm"
                                    @click="submitAvatarImage"
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
                        <h2 class="font-bold text-xl">{{ user.name }}</h2>
                        <div class="mr-4">
                            <PrimaryButton v-if="isMyProfile">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-4 h-4 mr-2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                                    />
                                </svg>
                                Edit Profile
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>

            <div class="">
                <TabGroup>
                    <TabList class="pl-[200px] flex bg-gray">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>

                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followers" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followings" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab
                            v-if="isMyProfile"
                            v-slot="{ selected }"
                            as="template"
                        >
                            <TabItem text="My Profile" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel class="bg-white p-3 shadow"> Posts </TabPanel>

                        <TabPanel class="bg-white p-3 shadow">
                            Follower Content
                        </TabPanel>

                        <TabPanel class="bg-white p-3 shadow">
                            Following Content
                        </TabPanel>

                        <TabPanel class="bg-white p-3 shadow">
                            Photos
                        </TabPanel>
                        <TabPanel
                            v-if="authUser && authUser.id === user.id"
                            class="bg-white p-3 shadow"
                        >
                            <Edit
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
