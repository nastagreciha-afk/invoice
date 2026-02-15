<script setup lang="ts">
import { ref } from 'vue'
import { useFormatters } from '~/composables/useFormatters'
import StatusBadge from '~/components/StatusBadge.vue'

const props = defineProps<{
  invoices: Array<{
    id: number
    number: string
    supplier_name: string
    gross_amount: number
    currency: string
    status: 'pending' | 'approved' | 'rejected'
    due_date: string
  }>
  loading: boolean
  error: string | null
}>()

const emit = defineEmits(['refresh', 'row-click'])

const { formatCurrency, formatDate } = useFormatters()

const handleRowClick = (invoiceId: number) => {
  emit('row-click', invoiceId)
}
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <!-- Loading state -->
    <div v-if="loading" class="p-8 text-center">
      <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
      <p class="mt-4 text-sm text-gray-500">Loading invoices...</p>
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
          <h3 class="text-sm font-medium text-red-800">Error loading invoices</h3>
          <div class="mt-2 text-sm text-red-700">
            <p>{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!invoices || invoices.length === 0" class="p-8 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-4 text-sm font-medium text-gray-900">No invoices found</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by creating a new invoice.</p>
    </div>

    <!-- Invoice table -->
    <div v-else>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
              Invoice Number
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
              Supplier
            </th>
            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
              Gross Amount
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
              Status
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
              Due Date
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          <tr
            v-for="invoice in invoices"
            :key="invoice.id"
            class="cursor-pointer hover:bg-gray-50"
            @click="handleRowClick(invoice.id)"
          >
            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
              {{ invoice.number }}
            </td>
            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
              {{ invoice.supplier_name }}
            </td>
            <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-right tabular-nums text-gray-900">
              {{ formatCurrency(invoice.gross_amount, invoice.currency) }}
            </td>
            <td class="whitespace-nowrap px-4 py-4 text-sm">
              <StatusBadge :status="invoice.status" />
            </td>
            <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-700">
              {{ formatDate(invoice.due_date) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>