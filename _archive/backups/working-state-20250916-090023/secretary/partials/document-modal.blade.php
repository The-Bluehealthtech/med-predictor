<!-- Modal Upload Document -->
<div x-data="{ show: false }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form method="POST" action="{{ route('secretary.documents.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-file-upload text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Upload de Document
                            </h3>
                            <div class="mt-2 space-y-4">
                                <!-- Athlète -->
                                <div>
                                    <label for="document_athlete_id" class="block text-sm font-medium text-gray-700">Athlète</label>
                                    <select id="document_athlete_id" name="athlete_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                                        <option value="">Sélectionner un athlète</option>
                                        @foreach($athletes ?? [] as $athlete)
                                            <option value="{{ $athlete->id }}">{{ $athlete->name }} ({{ $athlete->fifa_connect_id }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Type de Document -->
                                <div>
                                    <label for="document_type" class="block text-sm font-medium text-gray-700">Type de Document</label>
                                    <select id="document_type" name="document_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                                        <option value="">Sélectionner un type</option>
                                        <option value="medical_record">Dossier médical</option>
                                        <option value="radiology">Imagerie médicale</option>
                                        <option value="laboratory">Résultats de laboratoire</option>
                                        <option value="prescription">Ordonnance</option>
                                        <option value="certificate">Certificat médical</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>

                                <!-- Titre -->
                                <div>
                                    <label for="document_title" class="block text-sm font-medium text-gray-700">Titre du Document</label>
                                    <input type="text" id="document_title" name="title" placeholder="Titre du document" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </div>

                                <!-- Fichier -->
                                <div>
                                    <label for="document_file" class="block text-sm font-medium text-gray-700">Fichier</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl"></i>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                                    <span>Télécharger un fichier</span>
                                                    <input id="document_file" name="document_file" type="file" class="sr-only" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dicom">
                                                </label>
                                                <p class="pl-1">ou glisser-déposer</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PDF, DOC, images jusqu'à 10MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="document_description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="document_description" name="description" rows="3" placeholder="Description du document..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Uploader le document
                    </button>
                    <button type="button" @click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 