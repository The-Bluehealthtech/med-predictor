<template>
  <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-6">
    <!-- Header -->
    <div class="border-b border-gray-200 pb-4 mb-6">
      <h2 class="text-2xl font-bold text-gray-900">🏥 Medical Fitness Assessment - Doctor Sign-Off</h2>
      <p class="text-gray-600 mt-2">Final review and digital signature required for medical clearance</p>
    </div>

    <!-- Summary Block -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold text-blue-900 mb-3">📋 Assessment Summary</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="font-semibold text-gray-700">Player Name:</span>
          <span class="ml-2 text-gray-900">{{ playerName }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">Fitness Decision:</span>
          <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold" 
                :class="fitnessDecisionClass">
            {{ fitnessDecision }}
          </span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">Date of Examination:</span>
          <span class="ml-2 text-gray-900">{{ examinationDate }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">Assessment ID:</span>
          <span class="ml-2 text-gray-900">{{ assessmentId }}</span>
        </div>
      </div>
      
      <div class="mt-3" v-if="clinicalNotes">
        <span class="font-semibold text-gray-700">Clinical Notes:</span>
        <p class="mt-1 text-gray-700 text-sm">{{ clinicalNotes }}</p>
      </div>
    </div>

    <!-- Legal Declaration -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚖️ Legal Declaration</h3>
      <div class="flex items-start space-x-3">
        <input 
          type="checkbox" 
          id="legal-declaration"
          v-model="legalDeclarationAccepted"
          class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          :disabled="isProcessing"
        >
        <label for="legal-declaration" class="text-sm text-gray-700 leading-relaxed">
          I, the undersigned medical professional, confirm that I have reviewed the complete clinical information 
          and assume full responsibility for the fitness decision rendered herein. I understand that this assessment 
          will be used for professional football eligibility determination and I certify that all information provided 
          is accurate to the best of my medical knowledge.
        </label>
      </div>
    </div>

    <!-- Signature Capture -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-3">✍️ Digital Signature</h3>
      
      <!-- Signature Canvas -->
      <div class="border border-gray-300 rounded-lg bg-white p-4">
        <canvas 
          ref="signatureCanvas"
          class="border border-gray-300 rounded w-full h-48 cursor-crosshair"
          @mousedown="startDrawing"
          @mousemove="draw"
          @mouseup="stopDrawing"
          @mouseleave="stopDrawing"
          @touchstart="startDrawingTouch"
          @touchmove="drawTouch"
          @touchend="stopDrawing"
        ></canvas>
        
        <!-- Signature Controls -->
        <div class="flex justify-between items-center mt-3">
          <div class="text-sm text-gray-600">
            {{ signatureStatus }}
          </div>
          <div class="flex space-x-2">
            <button 
              @click="clearSignature"
              class="px-3 py-1 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded transition duration-200"
              :disabled="isProcessing"
            >
              🗑️ Clear
            </button>
            <button 
              @click="confirmSignature"
              class="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded transition duration-200"
              :disabled="!hasSignature || isProcessing"
            >
              ✅ Confirm
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Doctor Information -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
      <h3 class="text-lg font-semibold text-green-900 mb-3">👨‍⚕️ Doctor Information</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="font-semibold text-gray-700">Doctor Name:</span>
          <span class="ml-2 text-gray-900">{{ doctorName }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">License Number:</span>
          <span class="ml-2 text-gray-900">{{ licenseNumber }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">Timestamp:</span>
          <span class="ml-2 text-gray-900">{{ currentTimestamp }}</span>
        </div>
        <div>
          <span class="font-semibold text-gray-700">IP Address:</span>
          <span class="ml-2 text-gray-900">{{ ipAddress }}</span>
        </div>
      </div>
    </div>

    <!-- Final Action -->
    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
      <div class="text-sm text-gray-500">
        Status: {{ actionStatus }}
      </div>
      <button 
        @click="handleSignOff"
        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 flex items-center"
        :disabled="!canProceed || isProcessing"
      >
        <svg v-if="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ isProcessing ? 'Processing...' : 'Confirm and Sign' }}
      </button>
    </div>

    <!-- Re-authentication Modal -->
    <div v-if="showReAuthModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🔐 Re-authentication Required</h3>
        <p class="text-sm text-gray-600 mb-4">
          Please enter your password to confirm the medical assessment signature.
        </p>
        <input 
          type="password" 
          v-model="reAuthPassword"
          placeholder="Enter your password"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4"
        >
        <div class="flex justify-end space-x-3">
          <button 
            @click="cancelReAuth"
            class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-600 text-white rounded transition duration-200"
          >
            Cancel
          </button>
          <button 
            @click="confirmReAuth"
            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition duration-200"
            :disabled="!reAuthPassword"
          >
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue'

export default {
  name: 'DoctorSignOff',
  props: {
    playerName: {
      type: String,
      required: true
    },
    fitnessDecision: {
      type: String,
      required: true,
      validator: (value) => ['FIT', 'NOT_FIT', 'CONDITIONAL'].includes(value)
    },
    examinationDate: {
      type: String,
      required: true
    },
    assessmentId: {
      type: String,
      required: true
    },
    clinicalNotes: {
      type: String,
      default: ''
    },
    doctorName: {
      type: String,
      required: true
    },
    licenseNumber: {
      type: String,
      required: true
    }
  },
  emits: ['signed'],
  setup(props, { emit }) {
    // Reactive data
    const signatureCanvas = ref(null)
    const isDrawing = ref(false)
    const hasSignature = ref(false)
    const signatureConfirmed = ref(false)
    const legalDeclarationAccepted = ref(false)
    const isProcessing = ref(false)
    const showReAuthModal = ref(false)
    const reAuthPassword = ref('')
    const canvasContext = ref(null)

    // Computed properties
    const fitnessDecisionClass = computed(() => {
      switch (props.fitnessDecision) {
        case 'FIT':
          return 'bg-green-100 text-green-800'
        case 'NOT_FIT':
          return 'bg-red-100 text-red-800'
        case 'CONDITIONAL':
          return 'bg-yellow-100 text-yellow-800'
        default:
          return 'bg-gray-100 text-gray-800'
      }
    })

    const currentTimestamp = computed(() => {
      return new Date().toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      })
    })

    const ipAddress = computed(() => {
      // Mock IP address - in production, this would come from the server
      return '192.168.1.100'
    })

    const signatureStatus = computed(() => {
      if (!hasSignature.value) return 'No signature captured'
      if (!signatureConfirmed.value) return 'Signature captured - please confirm'
      return 'Signature confirmed ✓'
    })

    const actionStatus = computed(() => {
      if (!legalDeclarationAccepted.value) return 'Legal declaration required'
      if (!signatureConfirmed.value) return 'Signature confirmation required'
      if (isProcessing.value) return 'Processing signature...'
      return 'Ready to sign'
    })

    const canProceed = computed(() => {
      return legalDeclarationAccepted.value && signatureConfirmed.value && !isProcessing.value
    })

    // Methods
    const initCanvas = () => {
      if (signatureCanvas.value) {
        const canvas = signatureCanvas.value
        canvas.width = canvas.offsetWidth
        canvas.height = canvas.offsetHeight
        canvasContext.value = canvas.getContext('2d')
        canvasContext.value.strokeStyle = '#1f2937'
        canvasContext.value.lineWidth = 2
        canvasContext.value.lineCap = 'round'
      }
    }

    const startDrawing = (event) => {
      isDrawing.value = true
      const rect = signatureCanvas.value.getBoundingClientRect()
      const x = event.clientX - rect.left
      const y = event.clientY - rect.top
      canvasContext.value.beginPath()
      canvasContext.value.moveTo(x, y)
    }

    const draw = (event) => {
      if (!isDrawing.value) return
      const rect = signatureCanvas.value.getBoundingClientRect()
      const x = event.clientX - rect.left
      const y = event.clientY - rect.top
      canvasContext.value.lineTo(x, y)
      canvasContext.value.stroke()
    }

    const startDrawingTouch = (event) => {
      event.preventDefault()
      isDrawing.value = true
      const rect = signatureCanvas.value.getBoundingClientRect()
      const touch = event.touches[0]
      const x = touch.clientX - rect.left
      const y = touch.clientY - rect.top
      canvasContext.value.beginPath()
      canvasContext.value.moveTo(x, y)
    }

    const drawTouch = (event) => {
      event.preventDefault()
      if (!isDrawing.value) return
      const rect = signatureCanvas.value.getBoundingClientRect()
      const touch = event.touches[0]
      const x = touch.clientX - rect.left
      const y = touch.clientY - rect.top
      canvasContext.value.lineTo(x, y)
      canvasContext.value.stroke()
    }

    const stopDrawing = () => {
      if (isDrawing.value) {
        isDrawing.value = false
        hasSignature.value = true
        signatureConfirmed.value = false
      }
    }

    const clearSignature = () => {
      if (canvasContext.value) {
        canvasContext.value.clearRect(0, 0, signatureCanvas.value.width, signatureCanvas.value.height)
        hasSignature.value = false
        signatureConfirmed.value = false
      }
    }

    const confirmSignature = () => {
      if (hasSignature.value) {
        signatureConfirmed.value = true
      }
    }

    const getSignatureAsBase64 = () => {
      if (signatureCanvas.value) {
        return signatureCanvas.value.toDataURL('image/png')
      }
      return null
    }

    const handleSignOff = () => {
      if (!canProceed.value) return
      
      showReAuthModal.value = true
    }

    const confirmReAuth = async () => {
      if (!reAuthPassword.value) return
      
      isProcessing.value = true
      showReAuthModal.value = false
      
      // Simulate authentication check
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Emit the signed event
      const signatureImage = getSignatureAsBase64()
      
      emit('signed', {
        signedBy: props.doctorName,
        licenseNumber: props.licenseNumber,
        signedAt: new Date().toISOString(),
        signatureImage: signatureImage,
        fitnessStatus: props.fitnessDecision,
        assessmentId: props.assessmentId,
        playerName: props.playerName,
        examinationDate: props.examinationDate,
        ipAddress: ipAddress.value
      })
      
      isProcessing.value = false
      reAuthPassword.value = ''
    }

    const cancelReAuth = () => {
      showReAuthModal.value = false
      reAuthPassword.value = ''
    }

    // Lifecycle
    onMounted(() => {
      nextTick(() => {
        initCanvas()
      })
    })

    return {
      signatureCanvas,
      isDrawing,
      hasSignature,
      signatureConfirmed,
      legalDeclarationAccepted,
      isProcessing,
      showReAuthModal,
      reAuthPassword,
      fitnessDecisionClass,
      currentTimestamp,
      ipAddress,
      signatureStatus,
      actionStatus,
      canProceed,
      startDrawing,
      draw,
      startDrawingTouch,
      drawTouch,
      stopDrawing,
      clearSignature,
      confirmSignature,
      handleSignOff,
      confirmReAuth,
      cancelReAuth
    }
  }
}
</script>

<style scoped>
canvas {
  touch-action: none;
}
</style> 