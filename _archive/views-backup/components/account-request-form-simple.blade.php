<div class="bg-white rounded-lg shadow-xl p-8 max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-gray-900 mb-4">
            Demander un Accès Compte
        </h3>
        <p class="text-gray-600">
            Décrivez votre organisation et le type de football pour commencer avec la plateforme FIT
        </p>
    </div>

    <form method="POST" action="/account-request" class="space-y-8">
        @csrf
        
        <!-- Personal Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations Personnelles</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Prénom *
                    </label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom *
                    </label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>

        <!-- Organization Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations de l'Organisation</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'Organisation
                    </label>
                    <input 
                        type="text" 
                        id="organization_name" 
                        name="organization_name"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type d'Organisation
                    </label>
                    <select 
                        id="organization_type" 
                        name="organization_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner un type</option>
                        <option value="club">Club de Football</option>
                        <option value="association">Association Nationale</option>
                        <option value="federation">Fédération</option>
                        <option value="league">Ligue</option>
                        <option value="academy">Académie</option>
                        <option value="school">École de Football</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="football_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de Football
                    </label>
                    <select 
                        id="football_type" 
                        name="football_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner un type</option>
                        <option value="11-a-side">Football 11 à 11</option>
                        <option value="futsal">Futsal</option>
                        <option value="women">Football Féminin</option>
                        <option value="beach-soccer">Beach Soccer</option>
                        <option value="indoor">Football en Salle</option>
                        <option value="street">Street Football</option>
                    </select>
                </div>

                <div>
                    <label for="association_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Association FIFA
                    </label>
                    <select 
                        id="association_id" 
                        name="association_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner une association</option>
                        <optgroup label="UEFA">
                            <option value="fra">Fédération Française de Football</option>
                            <option value="ger">Deutscher Fußball-Bund</option>
                            <option value="esp">Real Federación Española de Fútbol</option>
                            <option value="ita">Federazione Italiana Giuoco Calcio</option>
                            <option value="eng">The Football Association</option>
                        </optgroup>
                        <optgroup label="CONMEBOL">
                            <option value="bra">Confederação Brasileira de Futebol</option>
                            <option value="arg">Asociación del Fútbol Argentino</option>
                            <option value="uru">Asociación Uruguaya de Fútbol</option>
                            <option value="chi">Federación de Fútbol de Chile</option>
                            <option value="col">Federación Colombiana de Fútbol</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="fifa_connect_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de Connexion FIFA
                    </label>
                    <select 
                        id="fifa_connect_type" 
                        name="fifa_connect_type"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Sélectionner un type</option>
                        <option value="club_admin">Administrateur de Club</option>
                        <option value="club_manager">Manager de Club</option>
                        <option value="club_medical">Staff Médical de Club</option>
                        <option value="association_admin">Administrateur d'Association</option>
                        <option value="association_registrar">Registraire d'Association</option>
                        <option value="association_medical">Staff Médical d'Association</option>
                        <option value="referee">Arbitre</option>
                        <option value="assistant_referee">Arbitre Assistant</option>
                        <option value="fourth_official">4ème Arbitre</option>
                        <option value="var_official">Officiel VAR</option>
                        <option value="match_commissioner">Commissaire de Match</option>
                        <option value="match_official">Officiel de Match</option>
                        <option value="team_doctor">Médecin d'Équipe</option>
                        <option value="physiotherapist">Physiothérapeute</option>
                        <option value="sports_scientist">Scientifique du Sport</option>
                    </select>
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        Ville
                    </label>
                    <input 
                        type="text" 
                        id="city" 
                        name="city"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Informations Supplémentaires</h4>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description / Commentaires
                </label>
                <textarea 
                    id="description" 
                    name="description"
                    rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Décrivez votre organisation, vos besoins ou toute information supplémentaire..."
                ></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-center pt-6">
            <button 
                type="submit" 
                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-lg font-medium"
            >
                Soumettre la Demande
            </button>
        </div>
    </form>
</div>
