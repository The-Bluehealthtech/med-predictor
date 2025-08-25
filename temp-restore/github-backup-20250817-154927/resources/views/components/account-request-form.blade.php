<div x-data="accountRequestForm()" class="bg-white rounded-lg shadow-xl p-8 max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-gray-900 mb-4">
            @if(app()->getLocale() == 'en')
                Request Account Access
            @else
                Demander un Accès Compte
            @endif
        </h3>
        <p class="text-gray-600">
            @if(app()->getLocale() == 'en')
                Tell us about your organization and football type to get started with FIT Platform
            @else
                Décrivez votre organisation et le type de football pour commencer avec la plateforme FIT
            @endif
        </p>
    </div>

    <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Personal Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        First Name *
                    @else
                        Prénom *
                    @endif
                </label>
                <input 
                    type="text" 
                    id="first_name" 
                    x-model="form.first_name"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.first_name }"
                    required
                >
                <div x-show="errors.first_name" x-text="errors.first_name" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        Last Name *
                    @else
                        Nom *
                    @endif
                </label>
                <input 
                    type="text" 
                    id="last_name" 
                    x-model="form.last_name"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.last_name }"
                    required
                >
                <div x-show="errors.last_name" x-text="errors.last_name" class="text-red-500 text-sm mt-1"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email *
                </label>
                <input 
                    type="email" 
                    id="email" 
                    x-model="form.email"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.email }"
                    required
                >
                <div x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        Phone
                    @else
                        Téléphone
                    @endif
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    x-model="form.phone"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.phone }"
                >
                <div x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></div>
            </div>
        </div>

        <!-- Organization Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        Organization Name *
                    @else
                        Nom de l'Organisation *
                    @endif
                </label>
                <input 
                    type="text" 
                    id="organization_name" 
                    x-model="form.organization_name"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.organization_name }"
                    required
                >
                <div x-show="errors.organization_name" x-text="errors.organization_name" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div>
                <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        Organization Type *
                    @else
                        Type d'Organisation *
                    @endif
                </label>
                <select 
                    id="organization_type" 
                    x-model="form.organization_type"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.organization_type }"
                    required
                >
                    <option value="">
                        @if(app()->getLocale() == 'en')
                            Select organization type
                        @else
                            Sélectionner le type d'organisation
                        @endif
                    </option>
                    <template x-for="(label, value) in organizationTypes" :key="value">
                        <option :value="value" x-text="label"></option>
                    </template>
                </select>
                <div x-show="errors.organization_type" x-text="errors.organization_type" class="text-red-500 text-sm mt-1"></div>
            </div>
        </div>

        <!-- Football Type Selection -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                @if(app()->getLocale() == 'en')
                    Football Type *
                @else
                    Type de Football *
                @endif
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="(label, value) in footballTypes" :key="value">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors"
                           :class="{ 'border-blue-500 bg-blue-50': form.football_type === value }">
                        <input 
                            type="radio" 
                            :value="value" 
                            x-model="form.football_type"
                            class="sr-only"
                            required
                        >
                        <div class="flex items-center">
                            <div class="w-5 h-5 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center"
                                 :class="{ 'border-blue-500': form.football_type === value }">
                                <div x-show="form.football_type === value" class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            </div>
                            <span class="font-medium text-gray-900" x-text="label"></span>
                        </div>
                    </label>
                </template>
            </div>
            <div x-show="errors.football_type" x-text="errors.football_type" class="text-red-500 text-sm mt-2"></div>
        </div>

        <!-- Location Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="association_id" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        FIFA Association *
                    @else
                        Association FIFA *
                    @endif
                </label>
                <select 
                    id="association_id" 
                    x-model="form.association_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.association_id }"
                    required
                >
                    <option value="">
                        @if(app()->getLocale() == 'en')
                            Select FIFA Association
                        @else
                            Sélectionner l'Association FIFA
                        @endif
                    </option>
                    <template x-for="(associations, confederation) in fifaAssociations" :key="confederation">
                        <optgroup :label="confederation">
                            <template x-for="association in associations" :key="association.id">
                                <option :value="association.id" x-text="association.full_name"></option>
                            </template>
                        </optgroup>
                    </template>
                </select>
                <div x-show="errors.association_id" x-text="errors.association_id" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                    @if(app()->getLocale() == 'en')
                        City
                    @else
                        Ville
                    @endif
                </label>
                <input 
                    type="text" 
                    id="city" 
                    x-model="form.city"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.city }"
                >
                <div x-show="errors.city" x-text="errors.city" class="text-red-500 text-sm mt-1"></div>
            </div>
        </div>

        <!-- FIFA Connect Type Selection -->
        <div class="bg-blue-50 p-6 rounded-lg">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">
                @if(app()->getLocale() == 'en')
                    FIFA Connect Type *
                @else
                    Type FIFA Connect *
                @endif
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="(label, value) in fifaConnectTypes" :key="value">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors"
                           :class="{ 'border-blue-500 bg-blue-50': form.fifa_connect_type === value }">
                        <input 
                            type="radio" 
                            :value="value" 
                            x-model="form.fifa_connect_type"
                            class="sr-only"
                            required
                        >
                        <div class="flex items-center">
                            <div class="w-5 h-5 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center"
                                 :class="{ 'border-blue-500': form.fifa_connect_type === value }">
                                <div x-show="form.fifa_connect_type === value" class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            </div>
                            <span class="font-medium text-gray-900" x-text="label"></span>
                        </div>
                    </label>
                </template>
            </div>
            <div x-show="errors.fifa_connect_type" x-text="errors.fifa_connect_type" class="text-red-500 text-sm mt-2"></div>
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                @if(app()->getLocale() == 'en')
                    Additional Information
                @else
                    Informations Supplémentaires
                @endif
            </label>
            <textarea 
                id="description" 
                x-model="form.description"
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.description }"
                placeholder="@if(app()->getLocale() == 'en')Tell us more about your organization and needs...@else Décrivez votre organisation et vos besoins...@endif"
            ></textarea>
            <div x-show="errors.description" x-text="errors.description" class="text-red-500 text-sm mt-1"></div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-6">
            <button 
                type="button" 
                @click="$dispatch('close-modal')"
                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
                @if(app()->getLocale() == 'en')
                    Cancel
                @else
                    Annuler
                @endif
            </button>
            
            <button 
                type="submit" 
                :disabled="loading"
                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
            >
                <div x-show="loading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                <span x-text="loading ? '{{ app()->getLocale() == "en" ? "Submitting..." : "Envoi en cours..." }}' : '{{ app()->getLocale() == "en" ? "Submit Request" : "Soumettre la Demande" }}'"></span>
            </button>
        </div>
    </form>

    <!-- Success Message -->
    <div x-show="success" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                @if(app()->getLocale() == 'en')
                    Request Submitted!
                @else
                    Demande Soumise !
                @endif
            </h3>
            <p class="text-gray-600 mb-6" x-text="successMessage"></p>
            <button 
                @click="closeSuccess"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                @if(app()->getLocale() == 'en')
                    Close
                @else
                    Fermer
                @endif
            </button>
        </div>
    </div>
</div>

<script>
function accountRequestForm() {
    return {
        form: {
            first_name: '',
            last_name: '',
            email: '',
            phone: '',
            organization_name: '',
            organization_type: '',
            football_type: '',
            association_id: '',
            fifa_connect_type: '',
            city: '',
            description: ''
        },
        errors: {},
        loading: false,
        success: false,
        successMessage: '',
        footballTypes: {},
        organizationTypes: {},
        fifaAssociations: {}, // Added for FIFA Association selection
        fifaConnectTypes: {}, // Added for FIFA Connect Type selection

        async init() {
            await this.loadFootballTypes();
            await this.loadOrganizationTypes();
            await this.loadFifaAssociations(); // Load FIFA Associations
            await this.loadFifaConnectTypes(); // Load FIFA Connect Types
        },

        async loadFootballTypes() {
            try {
                const response = await fetch('/account-request/football-types');
                const data = await response.json();
                this.footballTypes = data.data;
            } catch (error) {
                console.error('Error loading football types:', error);
            }
        },

        async loadOrganizationTypes() {
            try {
                const response = await fetch('/account-request/organization-types');
                const data = await response.json();
                this.organizationTypes = data.data;
            } catch (error) {
                console.error('Error loading organization types:', error);
            }
        },

        async loadFifaAssociations() {
            try {
                const response = await fetch('/account-request/fifa-associations');
                const data = await response.json();
                this.fifaAssociations = data.data;
            } catch (error) {
                console.error('Error loading FIFA associations:', error);
            }
        },

        async loadFifaConnectTypes() {
            try {
                const response = await fetch('/account-request/fifa-connect-types');
                const data = await response.json();
                this.fifaConnectTypes = data.data;
            } catch (error) {
                console.error('Error loading FIFA connect types:', error);
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch('/account-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.successMessage = data.message;
                    this.success = true;
                    this.form = {
                        first_name: '',
                        last_name: '',
                        email: '',
                        phone: '',
                        organization_name: '',
                        organization_type: '',
                        football_type: '',
                        association_id: '',
                        fifa_connect_type: '',
                        city: '',
                        description: ''
                    };
                    
                    // Fermer automatiquement la modal après 3 secondes
                    setTimeout(() => {
                        this.closeSuccess();
                    }, 3000);
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        this.errors = { general: data.message };
                    }
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                this.errors = { general: '{{ app()->getLocale() == "en" ? "An error occurred. Please try again." : "Une erreur est survenue. Veuillez réessayer." }}' };
            } finally {
                this.loading = false;
            }
        },

        closeSuccess() {
            this.success = false;
            this.$dispatch('close-modal');
        }
    }
}
</script> 