<script setup>
import { Teleport, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
    DisclosureButton,
} from "@headlessui/vue";
import {
    ArrowUturnLeftIcon,
    PlusCircleIcon,
    XMarkIcon,
} from "@heroicons/vue/20/solid";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputTextarea from "@/Components/InputTextArea.vue";
import axiosClient from "@/axiosClient";
import GroupForm from "@/Components/app/GroupForm.vue";

const props = defineProps({
    modelValue: Boolean,
});

const form = useForm({
    name: "",
    auto_approval: true,
    about: "",
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

const emit = defineEmits(["update:modelValue", "hide", "create"]);

function closeModal() {
    show.value = false;
    emit("hide");
    resetModal();
}

function resetModal() {
    form.reset();
    // formErrors.value = {};
}

function submit() {
    axiosClient.post(route("group.create"), form).then(({ data }) => {
        closeModal();
        console.log(data);
        emit("create", data);
    });
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
                                Create New Group
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
                            <div class="p-4 mt-2">
                                <GroupForm :form="form" />
                            </div>

                            <div class="flex justify-end gap-2 py-3 px-4">
                                <button
                                    @click="show = false"
                                    class="text-gray-800 flex gap-1 items-center justify-center bg-gray-100 rounded-md hover:bg-gray-200 py-2 px-4"
                                >
                                    <XMarkIcon class="w-5 h-5" />
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    class="flex items-center justify-center rounded-md border border-transparent bg-indigo-500 px-4 py-2 text-sm font-bold text-white hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
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
