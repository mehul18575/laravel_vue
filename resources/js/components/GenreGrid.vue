<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6" >
    <button v-for="genre in genres" :key="genre.id"
      class="bg-white hover:bg-gray-50 text-gray-900 font-semibold rounded-sm transition shadow-md px-2.5 h-[50px] flex items-center justify-between border border-gray-200 cursor-pointer"
      style="box-shadow: 0 2px 5px 0 rgba(211, 209, 238, 0.5);" @click="$emit('genre-selected', genre)">
      <div class="flex items-center text-gray-600">
        <svg class="w-8 h-8 mr-3 text-primary" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
            clip-rule="evenodd" />
        </svg>
        <span class="text-[20px]">{{ genre.name }}</span>
      </div>
      <svg width="19px" height="18px" viewBox="0 0 19 18" version="1.1" xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Icon/Next" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <path
            d="M9.34294737,-1.77635684e-15 L18.1578947,8.81621053 L9.34294737,17.6323579 L7.50003158,15.7894421 L13.159,10.131 L-8.8817842e-15,10.1310947 L-8.8817842e-15,7.49993684 L13.157,7.499 L7.50003158,1.84291579 L9.34294737,-1.77635684e-15 Z"
            id="Next" fill="#5E56E7" fill-rule="nonzero"></path>
        </g>
      </svg>
    </button>
  </div>
</template>

<script setup>
defineProps({
  genres: {
    type: Array,
    default: () => []
  }
})

defineEmits(['genre-selected'])
</script>