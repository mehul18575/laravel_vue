<template>
  <div class="flex justify-center items-center gap-4">
    <button
      @click="$emit('prev-page')"
      :disabled="currentPage === 1"
      class="px-4 py-2 bg-indigo-600 text-white rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed hover:bg-indigo-700 transition"
    >
      Previous
    </button>
    <span class="text-gray-600">
      Page {{ currentPage }} of {{ totalPages }}
    </span>
    <button
      @click="$emit('next-page')"
      :disabled="currentPage === totalPages"
      class="px-4 py-2 bg-indigo-600 text-white rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed hover:bg-indigo-700 transition"
    >
      Next
    </button>
  </div>
</template>

<script setup>
defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  }
})

defineEmits(['prev-page', 'next-page'])
</script>