<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import InvoiceDetails from '~/components/InvoiceDetails.vue'
import { useInvoices } from '~/composables/useInvoices'

const route = useRoute()
const router = useRouter()
const { getInvoice, isLoading, error } = useInvoices()

const invoice = ref<any>(null)
const invoiceId = computed(() => Number(route.params.id))

const fetchInvoice = async () => {
  try {
    const data = await getInvoice(invoiceId.value)
    invoice.value = data
  } catch (err) {
    console.error('Failed to fetch invoice:', err)
  }
}

const handleEdit = (id: number) => {
  router.push(`/invoices/${id}/edit`)
}

onMounted(() => {
  fetchInvoice()
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center gap-2">
        <button
          @click="() => router.push('/invoices')"
          class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Back to Invoices
        </button>
      </div>
      <h1 class="mt-4 text-2xl font-bold text-gray-900">Invoice Details</h1>
    </div>

    <!-- Invoice Details -->
    <InvoiceDetails
      :invoice="invoice"
      :loading="isLoading"
      :error="error"
      @edit="handleEdit"
    />
  </div>
</template>
