<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Arbitres</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                Mon Calendrier
            </h1>
            
            @if(isset($referee))
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-user-tie text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-600">Arbitre connecté</p>
                        <p class="text-lg font-bold text-blue-800">{{ $referee->name }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <p class="text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Vos assignations de matchs et votre planning personnel
                </p>
            </div>
            
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-calendar-check text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Mes Assignations</p>
                            <p id="totalAssignments" class="text-2xl font-bold text-blue-600">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Matchs Terminés</p>
                            <p id="completedMatches" class="text-2xl font-bold text-green-600">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600">Matchs à Venir</p>
                            <p id="upcomingMatches" class="text-2xl font-bold text-yellow-600">0</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des assignations -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-list text-blue-600 mr-2"></i>
                    Mes Assignations
                </h2>
                <div id="assignmentsList" class="space-y-4">
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>Chargement des assignations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAssignments();
        });

        async function loadAssignments() {
            try {
                const response = await fetch('/competitions/api/referee/calendar');
                if (response.ok) {
                    const assignments = await response.json();
                    updateStatistics(assignments);
                    renderAssignmentsList(assignments);
                } else {
                    console.error('Erreur lors du chargement des assignations');
                    document.getElementById('assignmentsList').innerHTML = 
                        '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Erreur lors du chargement</p></div>';
                }
            } catch (error) {
                console.error('Erreur:', error);
                document.getElementById('assignmentsList').innerHTML = 
                    '<div class="text-center text-red-500 py-8"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Erreur de connexion</p></div>';
            }
        }

        function updateStatistics(assignments) {
            document.getElementById('totalAssignments').textContent = assignments.length;
            
            const completed = assignments.filter(a => a.status === 'completed').length;
            document.getElementById('completedMatches').textContent = completed;
            
            const upcoming = assignments.filter(a => a.status === 'scheduled' || a.status === 'upcoming').length;
            document.getElementById('upcomingMatches').textContent = upcoming;
        }

        function renderAssignmentsList(assignments) {
            const container = document.getElementById('assignmentsList');
            
            if (assignments.length === 0) {
                container.innerHTML = 
                    '<div class="text-center text-gray-500 py-8"><i class="fas fa-calendar-times text-2xl mb-2"></i><p>Aucune assignation trouvée pour le moment</p><p class="text-sm">Vos prochains matchs apparaîtront ici</p></div>';
                return;
            }

            container.innerHTML = assignments.map(assignment => {
                const date = new Date(assignment.start);
                const formattedDate = date.toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                return `
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">${assignment.title}</h3>
                                <p class="text-sm text-gray-600">${assignment.competition}</p>
                                <p class="text-sm text-gray-500">${assignment.venue}</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-tie mr-1"></i>${assignment.referee_name}
                                    </span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        ${getRoleLabel(assignment.role)}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${formattedDate}</p>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusBadgeClass(assignment.status)}">
                                    ${getStatusLabel(assignment.status)}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'scheduled': 'bg-yellow-100 text-yellow-800',
                'upcoming': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'postponed': 'bg-orange-100 text-orange-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusLabel(status) {
            const labels = {
                'scheduled': 'Programmé',
                'upcoming': 'À venir',
                'completed': 'Terminé',
                'cancelled': 'Annulé',
                'postponed': 'Reporté'
            };
            return labels[status] || status;
        }

        function getRoleLabel(role) {
            const labels = {
                'main_referee': 'Arbitre Principal',
                'assistant_referee_1': 'Assistant 1',
                'assistant_referee_2': 'Assistant 2',
                'fourth_official': '4ème Arbitre'
            };
            return labels[role] || role;
        }
    </script>
</body>
</html>
            const classes = {
                'scheduled': 'bg-yellow-100 text-yellow-800',
                'upcoming': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'postponed': 'bg-orange-100 text-orange-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }
        function getStatusLabel(status) {
            const labels = {
                'scheduled': 'Programmé',
                'upcoming': 'À venir',
                'completed': 'Terminé',
                'cancelled': 'Annulé',
                'postponed': 'Reporté'
            };
            return labels[status] || status;
        }

        function getRoleLabel(role) {
            const labels = {
                'main_referee': 'Arbitre Principal',
                'assistant_referee_1': 'Assistant 1',
                'assistant_referee_2': 'Assistant 2',
                'fourth_official': '4ème Arbitre'
            };
            return labels[role] || role;
        }
    </script>
</body>
</html>
