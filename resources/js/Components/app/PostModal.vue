<script setup>
import { Teleport, computed, ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import {
    ArrowUturnLeftIcon,
    PaperClipIcon,
    PlusCircleIcon,
    XMarkIcon,
} from "@heroicons/vue/20/solid";
import AccountItem from "./AccountItem.vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { isImage } from "@/helpers";

const editor = ClassicEditor;
const editorConfig = {
    mediaEmbed: {
        removeProviders: [
            "dailymotion",
            "spotify",
            "youtube",
            "vimeo",
            "instagram",
            "twitter",
            "googleMaps",
            "flickr",
            "facebook",
        ],
    },
    toolbar: [
        "bold",
        "italic",
        "|",
        "bulletedList",
        "numberedList",
        "|",
        "heading",
        "|",
        "outdent",
        "indent",
        "|",
        "link",
        "|",
        "blockQuote",
    ],
};

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    group: {
        type: Object,
        default: null,
    },
    modelValue: Boolean,
});

const attachmentExtensions = usePage().props.attachmentExtensions;
// file: File, mime: '', url: ''
const attachmentFiles = ref([]);
const attachmentErrors = ref([]);
const formErrors = ref({});

const form = useForm({
    body: "",
    attachments: [],
    deleted_file_id: [],
    group_id: null,
    _method: "POST",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const computedAttachments = computed(() => {
    return [...attachmentFiles.value, ...(props.post.attachments || [])];
});

const showExtensionsNotify = computed(() => {
    for (let myFile of attachmentFiles.value) {
        const file = myFile.file;
        let parts = file.name.split(".");
        let extension = parts.pop().toLowerCase();
        if (!attachmentExtensions.includes(extension)) {
            return true;
        }
    }
    return false;
});

const emit = defineEmits(["update:modelValue", "hide"]);

watch(
    () => props.post,
    (newValue, oldValue) => {
        // console.log(oldValue);
        // console.log(newValue);
        form.body = newValue.body || "";
    }
);

function closeModal() {
    console.log(123);
    show.value = false;
    emit("hide");
    resetModal();
}

function resetModal() {
    form.reset();
    attachmentFiles.value = [];
    attachmentErrors.value = [];
    formErrors.value = {};
    if (props.post.attachments) {
        props.post.attachments.forEach((file) => {
            return (file.deleted = false);
        });
    }
}

async function onChangeAttach($event) {
    for (const file of $event.target.files) {
        const myFile = {
            file,
            url: await readFile(file),
        };
        attachmentFiles.value.push(myFile);
    }
    $event.target.value = null;
    // 33p
}

async function readFile(file) {
    return new Promise((resolve, reject) => {
        // isImage(file)
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                resolve(reader.result);
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        } else {
            resolve(null);
        }
    });
}

function removeFile(myFile) {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(
            (f) => f !== myFile
        );
    } else {
        form.deleted_file_id.push(myFile.id);
        myFile.deleted = true;
    }
}

function undoDelete(myFile) {
    myFile.deleted = false;
    form.deleted_file_id = form.deleted_file_id.filter(
        (id) => myFile.id !== id
    );
}

function submit() {
    if (props.group) {
        form.group_id = props.group.id;
    }
    form.attachments = attachmentFiles.value.map((myFile) => myFile.file);
    if (props.post.id) {
        form._method = "PUT";
        // gui du lieu id o route web {post}
        form.post(route("post.update", props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                processErrors(errors);
            },
        });
    } else {
        form.post(route("post.create"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                processErrors(errors);
            },
        });
    }
}

function processErrors(errors) {
    formErrors.value = errors;
    for (const key in errors) {
        if (key.includes(".")) {
            const [field, index] = key.split(".");
            attachmentErrors.value[index] = errors[key];
        }
    }
}
</script>

<template>
    <Teleport to="body" />
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div
                    class="flex min-h-full items-center justify-center p-4 text-center"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                        >
                            <DialogTitle
                                as="h3"
                                class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 text-gray-900"
                            >
                                {{ post.id ? "Update Post" : "Create Post" }}
                                <button
                                    @click="closeModal"
                                    class="w-8 h-8 rounded-full hover:bg-black/5 transition flex items-center justify-center"
                                >
                                    <ArrowUturnLeftIcon
                                        @click="closeModal"
                                        class="w-4 h-4"
                                    />
                                </button>
                            </DialogTitle>
                            <div class="p-3 mt-2">
                                <!-- Account Item -->
                                <AccountItem
                                    :post="post"
                                    :showTime="false"
                                    class="mb-3"
                                />

                                <div
                                    v-if="formErrors.group_id"
                                    class="bg-red-400 py-2 px-3 rounded text-white mb-3"
                                >
                                    {{ formErrors.group_id }}
                                </div>

                                <!-- Ckeditor la` mot bo thiet ke giong wordpress -->
                                <ckeditor
                                    :editor="editor"
                                    v-model="form.body"
                                    :config="editorConfig"
                                ></ckeditor>

                                <!-- notify errors -->
                                <div
                                    v-if="showExtensionsNotify"
                                    class="border-l-4 bg-sky-100 mt-3 text-gray-800 border-sky-500 py-2 px-3"
                                >
                                    Files must be one of the following
                                    extensions
                                    <small>{{
                                        attachmentExtensions.join(", ")
                                    }}</small>
                                </div>
                                <div
                                    v-if="formErrors.attachments"
                                    class="border-l-4 bg-amber-100 mt-3 text-gray-800 border-amber-500 py-2 px-3"
                                >
                                    {{ formErrors.attachments }}
                                </div>
                                <!-- inputArea -->
                                <!-- <InputTextArea
                                    class="mb-3 w-full"
                                    v-model="post.body"
                                /> -->
                                <!-- Post -->
                                <div
                                    class="grid gap-3 my-3"
                                    :class="[
                                        computedAttachments.length === 1
                                            ? 'grid-cols-1'
                                            : 'grid-cols-2',
                                    ]"
                                >
                                    <div
                                        v-for="(
                                            myFile, index
                                        ) of computedAttachments"
                                    >
                                        <div
                                            class="group bg-blue-100 aspect-square relative flex flex-col items-center justify-center text-gray-500 border-2"
                                            :class="
                                                attachmentErrors[index]
                                                    ? 'border-red-500'
                                                    : ''
                                            "
                                        >
                                            <!-- <div
                                                v-if="index === 3"
                                                class="absolute left-0 right-0 top-0 bottom-0 z-10 bg-black/60 text-white flex items-center justify-center text-2xl"
                                            >
                                                +{{
                                                    computedAttachments.length -
                                                    4
                                                }}
                                                more
                                            </div> -->

                                            <div
                                                v-if="myFile.deleted"
                                                class="z-10 flex justify-between items-center absolute left-0 bottom-0 right-0 py-2 px-3 text-sm bg-black text-white"
                                            >
                                                To be deleted
                                                <ArrowUturnLeftIcon
                                                    @click="undoDelete(myFile)"
                                                    class="w-4 h-4 cursor-pointer"
                                                />
                                            </div>

                                            <button
                                                @click="removeFile(myFile)"
                                                class="absolute z-20 right-3 top-3 w-7 h-7 flex items-center justify-center bg-black/30 text-white rounded-full hover:bg-black/40"
                                            >
                                                <XMarkIcon class="h-5 w-5" />
                                            </button>
                                            <img
                                                v-if="
                                                    isImage(
                                                        myFile.file || myFile
                                                    )
                                                "
                                                :src="myFile.url"
                                                class="object-contain aspect-square"
                                                :class="
                                                    myFile.deleted
                                                        ? 'opacity-50'
                                                        : ''
                                                "
                                            />
                                            <div
                                                class="px-3 flex justify-center items-center flex-col"
                                                :class="
                                                    myFile.deleted
                                                        ? 'opacity-50'
                                                        : ''
                                                "
                                                v-else
                                            >
                                                <PaperClipIcon
                                                    class="w-10 h-10 mb-3"
                                                />
                                                <small class="text-center">
                                                    {{
                                                        (myFile.file || myFile)
                                                            .name
                                                    }}</small
                                                >
                                            </div>
                                        </div>
                                        <small class="text-red-500">
                                            {{ attachmentErrors[index] }}
                                        </small>
                                    </div>
                                </div>
                                <!-- Lap hinh anh khi edit -->
                            </div>

                            <div class="flex gap-2 py-3 px-4">
                                <button
                                    type="button"
                                    class="relative w-full flex items-center justify-center rounded-md border border-transparent bg-indigo-500 px-4 py-2 text-sm font-bold text-white hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    @click="submit"
                                >
                                    <PaperClipIcon class="w-4 h-4 mr-1" />
                                    Attach Files
                                    <input
                                        @click.stop
                                        @change="onChangeAttach"
                                        type="file"
                                        multiple
                                        class="absolute top-0 left-0 right-0 bottom-0 opacity-0"
                                    />
                                </button>
                                <button
                                    type="button"
                                    class="w-full flex items-center justify-center rounded-md border border-transparent bg-indigo-500 px-4 py-2 text-sm font-bold text-white hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    @click="submit"
                                >
                                    <PlusCircleIcon class="w-4 h-4 mr-1" />
                                    Submit
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <teleport to="body" />
</template>
