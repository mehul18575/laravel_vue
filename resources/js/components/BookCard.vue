<template>
  <div class="flex flex-col items-center cursor-pointer" @click="openBook(book)">
    <div class="bg-white rounded-lg overflow-hidden shadow-md w-[114px] h-[162px] mb-2 "
      style="box-shadow: 0 2px 5px 0 rgba(211, 209, 238, 0.5);">
      <div class="w-full h-full bg-gray-100 flex items-center justify-center relative">
        <img v-if="!imageError && book.cover_image_url" :src="book.cover_image_url" :alt="book.title"
          class="w-full h-full object-cover transition-opacity duration-300"
          :class="{ 'opacity-0': !imageLoaded, 'opacity-100': imageLoaded }"
          :loading="priority === 'high' ? 'eager' : 'lazy'" @error="imageError = true" @load="imageLoaded = true" />
        <div v-else class="text-gray-400 text-xs absolute inset-0 flex items-center justify-center bg-gray-100">
          <div v-if="!imageError" class="w-6 h-6 border-2 border-gray-300 border-t-gray-600 rounded-full animate-spin">
          </div>
          <span v-else>No Cover</span>
        </div>
      </div>
    </div>
    <div class="w-[114px] flex flex-col items-center text-center">
      <h3 class="text-gray-900 text-[12px] line-clamp-2 mb-1">
        {{ book.title }} </h3>
      <div class="text-[#A0A0A0] text-[12px] truncate max-w-[100px]">
        {{ authorNames }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  book: { type: Object, required: true },
  priority: { type: String, default: 'low' }
})

const imageError = ref(false)
const imageLoaded = ref(false)

const genreNames = computed(() => props.book.bookshelves?.map(a => a.name).join(', ') || 'N/A')
const authorNames = computed(() => props.book.authors?.map(a => a.name).join(', ') || 'N/A')

const openBook = (book) => {
  if (book.viewable_url) {
    window.open(book.viewable_url, '_blank', 'noopener,noreferrer')
  } else {
    alert('No viewable version available')
  }
}
</script>
