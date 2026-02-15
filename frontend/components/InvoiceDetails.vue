<script setup lang="ts">
import { useFormatters } from '~/composables/useFormatters'
import StatusBadge from '~/components/StatusBadge.vue'

const props = defineProps<{
  invoice: {
    id: number
    number: string
    supplier_name: string
    supplier_tax_id: string
    net_amount: number
    vat_amount: number
    gross_amount: number
    currency: string
    status: 'pending' | 'approved' | 'rejected'
    issue_date: string
    due_date: string
    created_at: string
    updated_at: string
  } | null
  loading: boolean
  error: string | null
}>()

const { formatCurrency, formatDate, formatDateTime } = useFormatters()

const emit = defineEmits(['edit'])

const handleEdit = () => {
  if (props.invoice) {
    emit('edit', props.invoice.id)
  }
}
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <!-- Loading state -->
    <div v-if="loading" class="p-8 text-center">
      <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
      <p class="mt-4 text-sm text-gray-500">Loading invoice details...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="border-l-4 border-red-400 bg-red-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 1a.75.75 0 01.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 01.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 01.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 01.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 01.75.75v.008c0 .414.336.75.75.75h.008c.414 0 .75-.336.75-.75V10.75A.75.75 0 0110 10z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Error loading invoice</h3>
          <div class="mt-2 text-sm text-red-700">
            <p>{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoice details -->
    <div v-else-if="invoice" class="divide-y divide-gray-100">
      <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-lg font-medium text-gray-900">Invoice {{ invoice.number }}</h2>
            <p class="mt-1 text-sm text-gray-500">
              <StatusBadge :status="invoice.status" />
            </p>
          </div>
          <button
            @click="handleEdit"
            class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Edit Invoice
          </button>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Supplier Name</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ invoice.supplier_name }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Supplier Tax ID</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ invoice.supplier_tax_id }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Net Amount</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatCurrency(invoice.net_amount, invoice.currency) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">VAT Amount</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatCurrency(invoice.vat_amount, invoice.currency) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Gross Amount</dt>
            <dd class="mt-1 text-sm font-medium text-gray-900">{{ formatCurrency(invoice.gross_amount, invoice.currency) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Currency</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ invoice.currency }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(invoice.issue_date) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Due Date</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(invoice.due_date) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Created At</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(invoice.created_at) }}</dd>
          </div>

          <div class="sm:col-span-1">
            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ formatDateTime(invoice.updated_at) }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="p-8 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-4 text-sm font-medium text-gray-900">Invoice not found</h3>
      <p class="mt-1 text-sm text-gray-500">The requested invoice does not exist.</p>
    </div>
  </div>
</template>