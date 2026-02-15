<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import * as zod from 'zod'
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
  } | null
  loading: boolean
  error: string | null
  onSubmit: (data: any) => Promise<void>
}>()

const emit = defineEmits(['cancel'])

// Form validation schema
const validationSchema = toTypedSchema(
  zod.object({
    net_amount: zod.number().min(0.01, 'Net amount must be greater than 0'),
    vat_amount: zod.number().min(0, 'VAT amount must be 0 or greater'),
    due_date: zod.string().refine((val) => {
      if (!props.invoice) return false
      const dueDate = new Date(val)
      const issueDate = new Date(props.invoice.issue_date)
      return dueDate >= issueDate
    }, 'Due date must be on or after issue date'),
  })
)

const { handleSubmit, setValues, values } = useForm({
  validationSchema,
})

// Calculate gross amount automatically
const grossAmount = computed(() => {
  const net = Number(values.net_amount) || 0
  const vat = Number(values.vat_amount) || 0
  return net + vat
})

// Watch for invoice changes and update form values
watch(() => props.invoice, (newInvoice) => {
  if (newInvoice) {
    setValues({
      net_amount: newInvoice.net_amount,
      vat_amount: newInvoice.vat_amount,
      due_date: newInvoice.due_date,
    })
  }
}, { immediate: true })

// Check if invoice can be edited
const canEdit = computed(() => {
  return props.invoice ? props.invoice.status === 'pending' : false
})

const onSubmitForm = handleSubmit(async (values) => {
  if (!props.invoice) return

  await props.onSubmit({
    ...props.invoice,
    net_amount: values.net_amount,
    vat_amount: values.vat_amount,
    gross_amount: grossAmount.value,
    due_date: values.due_date,
  })
})

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <!-- Loading state -->
    <div v-if="loading" class="p-8 text-center">
      <div class="mx-auto h-8 w-8 animate-spin rounded-full border-4 border-gray-300 border-t-blue-600"></div>
      <p class="mt-4 text-sm text-gray-500">Loading invoice...</p>
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

    <!-- Edit restriction message -->
    <div v-else-if="invoice && !canEdit" class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.828c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zM10 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-yellow-800">This invoice cannot be edited</h3>
          <div class="mt-2 text-sm text-yellow-700">
            <p>This invoice cannot be edited because its status is <StatusBadge :status="invoice.status" />.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit form -->
    <form v-else-if="invoice" @submit="onSubmitForm" class="divide-y divide-gray-100">
      <div class="px-4 py-5 sm:px-6">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-lg font-medium text-gray-900">Edit Invoice {{ invoice.number }}</h2>
            <p class="mt-1 text-sm text-gray-500">
              <StatusBadge :status="invoice.status" />
            </p>
          </div>
        </div>
      </div>

      <div class="px-4 py-5 sm:px-6">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="net_amount" class="block text-sm font-medium leading-6 text-gray-900">Net Amount</label>
            <div class="mt-2">
              <input
                type="number"
                step="0.01"
                id="net_amount"
                v-model="values.net_amount"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
              />
              <p class="mt-1 text-sm text-gray-500">Enter the net amount (without VAT)</p>
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="vat_amount" class="block text-sm font-medium leading-6 text-gray-900">VAT Amount</label>
            <div class="mt-2">
              <input
                type="number"
                step="0.01"
                id="vat_amount"
                v-model="values.vat_amount"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
              />
              <p class="mt-1 text-sm text-gray-500">Enter the VAT amount</p>
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="gross_amount" class="block text-sm font-medium leading-6 text-gray-900">Gross Amount</label>
            <div class="mt-2">
              <input
                type="text"
                id="gross_amount"
                :value="`${grossAmount} ${invoice.currency}`"
                readonly
                disabled
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 bg-gray-50"
              />
              <p class="mt-1 text-sm text-gray-500">Gross amount is calculated automatically (Net + VAT)</p>
            </div>
          </div>

          <div class="sm:col-span-3">
            <label for="due_date" class="block text-sm font-medium leading-6 text-gray-900">Due Date</label>
            <div class="mt-2">
              <input
                type="date"
                id="due_date"
                v-model="values.due_date"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
              />
              <p class="mt-1 text-sm text-gray-500">Issue date: {{ new Date(invoice.issue_date).toLocaleDateString() }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3 px-4 py-4 sm:px-6">
        <button
          type="button"
          @click="handleCancel"
          class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
        >
          Save Changes
        </button>
      </div>
    </form>
  </div>
</template>