<template>
  <div class="relative">
    <input v-model="localQuery" type="text" :placeholder="placeholder"
    class="w-full pl-10 pr-[10px] h-[40px] border border-gray-300 rounded-sm text-sm focus:border-[#5E56E7] outline-none transition-colors "

      @input="handleInput" @keyup.enter="handleSearch">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </div>
    <button v-if="localQuery" @click="clearSearch"
      class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600 transition-colors">
      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Search...'
  },
  buttonText: {
    type: String,
    default: 'Search'
  },
  debounceDelay: {
    type: Number,
    default: 300
  }
})

const emit = defineEmits(['update:modelValue', 'search', 'input'])

const localQuery = ref(props.modelValue)
let debounceTimer = null

watch(() => props.modelValue, (newValue) => {
  localQuery.value = newValue
})

const handleInput = () => {
  emit('update:modelValue', localQuery.value)
  emit('input', localQuery.value)
  console.log(localQuery.value);

  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  debounceTimer = setTimeout(() => {
    emit('search', localQuery.value)
  }, props.debounceDelay)
}

const handleSearch = () => {
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
  emit('update:modelValue', localQuery.value)
  emit('search', localQuery.value)
}

const clearSearch = () => {
  localQuery.value = ''
  emit('update:modelValue', '')
  emit('search', '')
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
}
</script>