<template>
  <div class="w-full md:pt-[80px] md:px-6">
    <div
      ref="scrollContainer"
      class="overflow-y-auto scrollbar-hide"
      @scroll="handleScroll"
      :style="{ maxHeight: maxHeight }"
    >
      <slot />
    </div>
    <div v-if="loading" class="flex justify-center items-center py-4">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
      <span class="ml-2 text-sm text-gray-600">Loading more books...</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  hasMore: {
    type: Boolean,
    default: true
  },
  threshold: {
    type: Number,
    default: 100
  },
  maxHeight: {
    type: String,
    default: '600px'
  }
})

const emit = defineEmits(['load-more'])

const scrollContainer = ref(null)
let isLoadingMore = false

defineExpose({
  resetLoadingState: () => {
    isLoadingMore = false
  }
})

const handleScroll = () => {
  if (!scrollContainer.value || props.loading || !props.hasMore || isLoadingMore) return

  const { scrollTop, scrollHeight, clientHeight } = scrollContainer.value
  const distanceFromBottom = scrollHeight - scrollTop - clientHeight

  if (distanceFromBottom < props.threshold) {
    isLoadingMore = true
    emit('load-more')
  }
}

let observer = null

onMounted(() => {
  if (scrollContainer.value) {
    if (window.IntersectionObserver) {
      observer = new IntersectionObserver(
        (entries) => {
          if (entries[0].isIntersecting && !props.loading && props.hasMore) {
            emit('load-more')
          }
        },
        { threshold: 0.1, root: scrollContainer.value }
      )
      const sentinel = document.createElement('div')
      sentinel.style.height = '10px'
      sentinel.style.width = '100%'
      scrollContainer.value.appendChild(sentinel)
      observer.observe(sentinel)
    }
  }
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
  }
})
</script>
