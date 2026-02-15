<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import InvoiceTable from '~/components/InvoiceTable.vue'
import { useInvoices } from '~/composables/useInvoices'

const router = useRouter()
const { getInvoices, isLoading, error } = useInvoices()

const invoices = ref<any[]>([])
const pagination = ref({
  current_page: 1,
  per_page: 20,
  total: 0,
  last_page: 1,
})

const fetchInvoices = async () => {
  try {
    const response = await getInvoices(1, 20)
    invoices.value = response.data
    pagination.value = response.meta
  } catch (err) {
    console.error('Failed to fetch invoices:', err)
  }
}

const handleRowClick = (invoiceId: number) => {
  router.push(`/invoices/${invoiceId}`)
}

onMounted(() => {
  fetchInvoices()
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between gap-4">
      <h1 class="text-2xl font-bold text-gray-900">Invoices</h1>
      <button
        type="button"
        class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        @click="fetchInvoices"
        :disabled="isLoading"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh
      </button>
    </div>

    <!-- Invoice Table -->
    <InvoiceTable
      :invoices="invoices"
      :loading="isLoading"
      :error="error"
      @row-click="handleRowClick"
    />
  </div>
</template>