<template>
    <header class="fixed top-0 left-0 w-full z-50 bg-white/90">
        <div v-if="!selectedGenre" class="relative text-gray-900">
            <div class="absolute inset-0 bg-cover bg-center"
                style="background-image: url('/images/Pattern.svg'); opacity: 0.6;"></div>
            <div class="relative md:py-16 py-12 px-6 max-w-6xl mx-auto text-left">
                <h1 class="text-[48px] text-[#5E56E7] font-[Montserrat] font-semibold">
                    Gutenberg Project
                </h1>
                <p class="text-[20px] font-semibold text-gray-800 mt-2 max-w-3xl">
                    A social cataloging website that allows you to freely search its
                    database of books, annotations, and reviews.
                </p>
            </div>
        </div><div  v-else class="relative max-w-full sm:max-w-2xl md:max-w-6xl mx-auto pt-6 md:pt-10 px-4 sm:px-6">

            <div class="mb-6 flex items-center">
                <button @click="emit('back')"
                    class="flex items-center text-gray-600 hover:text-gray-800 transition mr-4 cursor-pointer">
                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.3489391,-1.59872116e-14 L24,11.6527304 L12.3489391,23.3053774 L9.91308522,20.8695235 L17.393,13.39 L0,13.3906643 L0,9.91296 L17.391,9.912 L9.91308522,2.43585391 L12.3489391,-1.59872116e-14 Z"
                            fill="#5E56E7" transform="translate(12,11.65) scale(-1,1) translate(-12,-11.65)" />
                    </svg>
                </button>
                <h2 class="text-[30px] text-[#5E56E7] ml-4">
                    {{ selectedGenre.name }}
                </h2>
            </div>

            <SearchBar v-model="searchQuery" placeholder="Search books by title or author..." :debounce-delay="500"
                @search="handleSearch" class="mb-10" />
        </div>
    </header>
</template>


<script setup>
import { ref } from 'vue'
import SearchBar from './SearchBar.vue'

defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    showBackButton: {
        type: Boolean,
        default: false
    },
    selectedGenre: {
        type: Object,
        default: null
    }
})

const emit = defineEmits(['back', 'genre-selected', 'search'])

const searchQuery = ref('')

const handleSearch = (query) => {
    emit('search', query)
}
</script>
