<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  status: 'pending' | 'approved' | 'rejected'
}>()

const statusClasses = computed(() => {
  const baseClasses = 'px-2 py-1 rounded-full text-xs font-medium'
  
  switch (props.status) {
    case 'pending':
      return `${baseClasses} bg-yellow-100 text-yellow-800`
    case 'approved':
      return `${baseClasses} bg-green-100 text-green-800`
    case 'rejected':
      return `${baseClasses} bg-red-100 text-red-800`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`
  }
})

const statusText = computed(() => {
  switch (props.status) {
    case 'pending':
      return 'Pending'
    case 'approved':
      return 'Approved'
    case 'rejected':
      return 'Rejected'
    default:
      return props.status
  }
})
</script>

<template>
  <span :class="statusClasses">
    {{ statusText }}
  </span>
</template>