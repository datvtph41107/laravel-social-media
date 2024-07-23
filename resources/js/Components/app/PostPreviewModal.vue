<script setup>
import { Teleport, computed } from "vue";
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from "@headlessui/vue";
import {
    ArrowUturnLeftIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    PaperClipIcon,
} from "@heroicons/vue/20/solid";
import { isImage } from "@/helpers";

const props = defineProps({
    attachments: {
        type: Array,
        required: true,
    },
    index: Number,
    modelValue: Boolean,
});

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});

// prev next
const currentIndex = computed({
    get: () => props.index,
    set: (value) => emit("update:index", value),
});

const attachment = computed(() => {
    return props.attachments[currentIndex.value];
});

const emit = defineEmits(["update:modelValue", "update:index", "hide"]);

function closeModal() {
    show.value = false;
    emit("hide");
}

function prev() {
    if (currentIndex.value === 0) return;
    currentIndex.value--;
}

function next() {
    if (currentIndex.value === props.attachments.length - 1) return;
    currentIndex.value++;
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
                <div class="h-screen w-screen">
                    <TransitionChild
                        class="h-full w-full"
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="flex flex-col w-full transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                        >
                            <button
                                @click="closeModal"
                                class="absolute right-3 top-3 z-30 w-10 h-10 text-gray-100 rounded-full hover:bg-black/5 transition flex items-center justify-center"
                            >
                                <ArrowUturnLeftIcon
                                    @click="closeModal"
                                    class="w-6 h-6"
                                />
                            </button>

                            <div class="relative group h-full bg-slate-800">
                                <div
                                    @click="prev"
                                    class="absolute opacity-0 group-hover:opacity-100 text-gray-100 cursor-pointer flex items-center w-12 h-full left-0 bg-black/5"
                                >
                                    <ChevronLeftIcon class="w-12" />
                                </div>

                                <div
                                    @click="next"
                                    class="absolute opacity-0 group-hover:opacity-100 text-gray-100 cursor-pointer flex items-center w-12 h-full right-0 bg-black/5"
                                >
                                    <ChevronRightIcon class="w-12" />
                                </div>

                                <div
                                    class="flex items-center justify-center w-full h-full p-3"
                                >
                                    <img
                                        v-if="isImage(attachment)"
                                        :src="attachment.url"
                                        class="max-w-full max-h-full"
                                    />
                                    <div
                                        v-else
                                        class="p-32 bg-white flex flex-col justify-center items-center"
                                    >
                                        <PaperClipIcon class="w-10 h-10 mb-3" />
                                        <small>{{ attachment.name }}</small>
                                    </div>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
    <teleport to="body" />
</template>
