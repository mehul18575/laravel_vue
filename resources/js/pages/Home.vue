<template>
<main class="flex-1 md:pt-[60px]" :class="isCategorySelected ? 'pt-[120px]' : 'pt-[220px]'">
  <PageHeader 
      :title="pageTitle"
      :subtitle="pageSubtitle"
      :show-back-button="isCategorySelected"
      :selected-genre="selectedGenre"
      @back="backToGenres"
      @search="searchBooks"
    />

    <LoadingSpinner v-if="loading || booksLoading" />

    <template v-else>
      <GenreGrid 
        v-if="!isCategorySelected" 
        :genres="genres" 
        @genre-selected="selectGenre" 
        class="md:pt-[150px] px-6"
      />
      <template v-if="isCategorySelected">
        <template v-if="books.length > 0">
          <InfiniteScroll
            ref="infiniteScrollRef"
            :loading="booksLoading"
            :has-more="hasMoreBooks"
            :max-height="'80vh'"
            @load-more="loadMoreBooks"
          >
            <div class="grid grid-cols-3 md:grid-cols-6 gap-8">
              <BookCard v-for="(book, index) in books" :key="book.id" :book="book"
                :priority="index < 6 ? 'high' : 'low'" />
            </div>
          </InfiniteScroll>
        </template>
        <div v-else class="flex flex-col items-center justify-center text-center min-h-[50vh]">
          <p class="text-gray-600 text-lg">No books found.</p>
        </div>
      </template>
    </template>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import GenreGrid from '../components/GenreGrid.vue'
import BookCard from '../components/BookCard.vue'
import InfiniteScroll from '../components/InfiniteScroll.vue'
import PageHeader from '../components/PageHeader.vue'

const loading = ref(true)
const genres = ref([])
const selectedGenre = ref(null)
const books = ref([])
const booksLoading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const hasMoreBooks = ref(true)
const isCategorySelected = ref(false)
const infiniteScrollRef = ref(null)

const pageTitle = computed(() => {
  if (!isCategorySelected.value) {
    return 'Gutenberg Project'
  }
  return selectedGenre.value?.name || 'Search Results'
})

const pageSubtitle = computed(() => {
  if (searchQuery.value && selectedGenre.value) {
    return `Searching "${searchQuery.value}" in ${selectedGenre.value.name}`
  }
  if (searchQuery.value) {
    return `Search results for "${searchQuery.value}"`
  }
  return ''
})

const loadBooks = async (genreId = null, page = 1, search = '', append = false) => {
  try {
    booksLoading.value = true
    let url = `/api/books?page=${page}&per_page=25`

    if (genreId) {
      url += `&genre=${genreId}`
    }

    if (search) {
      url += `&search=${encodeURIComponent(search)}`
    }

    const res = await fetch(url)
    const data = await res.json()

    if (append) {
      books.value = [...books.value, ...data.data]
    } else {
      books.value = data.data
    }

    currentPage.value = data.meta.current_page
    totalPages.value = data.meta.total
    hasMoreBooks.value = data.meta.current_page < data.meta.total && data.data.length > 0
  } catch (error) {
    console.error('Failed to load books', error)
    hasMoreBooks.value = false
  } finally {
    booksLoading.value = false
  }
}

const selectGenre = (genre) => {
  selectedGenre.value = genre
  searchQuery.value = ''
  currentPage.value = 1
  isCategorySelected.value = true
  loadBooks(genre.id, 1, '')
}

const searchBooks = (query = '') => {
  const trimmedQuery = (query || searchQuery.value).trim()
  currentPage.value = 1
  isCategorySelected.value = true
  loadBooks(selectedGenre.value?.id || null, 1, trimmedQuery)
}

const loadMoreBooks = () => {
  if (!booksLoading.value && hasMoreBooks.value) {
    const genreId = selectedGenre.value?.id || null
    const nextPage = currentPage.value + 1
    loadBooks(genreId, nextPage, searchQuery.value, true).finally(() => {
      if (infiniteScrollRef.value) {
        infiniteScrollRef.value.resetLoadingState()
      }
    })
  }
}

const backToGenres = () => {
  currentPage.value = 1
  isCategorySelected.value = false
  selectedGenre.value = null
  searchQuery.value = ''
  books.value = []
}

onMounted(async () => {
  try {
    loading.value = true
    const res = await fetch('/api/genres')
    genres.value = await res.json()
  } catch (error) {
    console.error('Failed to load genres', error)
  } finally {
    loading.value = false
  }
})
</script>