/**
 * Module Compétitions - Cards Vue.js
 * Version: 1.0 - FIT Platform
 */

// Configuration Vue.js pour les compétitions
const CompetitionsApp = {
    data() {
        return {
            // Données des compétitions
            competitions: [],
            engagements: [],
            effectif: [],
            matchs: [],
            feuilles: [],
            sanctions: [],
            
            // États de chargement
            loading: {
                competitions: false,
                engagements: false,
                effectif: false,
                matchs: false,
                feuilles: false,
                sanctions: false
            },
            
            // Filtres
            filters: {
                statut: '',
                competition: '',
                joueur: '',
                date: ''
            },
            
            // Notifications
            notifications: [],
            
            // Modales
            modals: {
                feuilleMatch: false,
                detailsJoueur: false,
                reprogrammerMatch: false,
                ajouterSanction: false
            }
        }
    },
    
    mounted() {
        this.initializeApp();
    },
    
    methods: {
        // Initialisation de l'application
        initializeApp() {
            console.log('🏆 Module Compétitions initialisé');
            this.loadCompetitions();
            this.setupEventListeners();
        },
        
        // Chargement des compétitions
        async loadCompetitions() {
            this.loading.competitions = true;
            try {
                const response = await fetch('/api/competitions');
                this.competitions = await response.json();
                console.log('✅ Compétitions chargées:', this.competitions.length);
            } catch (error) {
                console.error('❌ Erreur chargement compétitions:', error);
                this.showNotification('Erreur lors du chargement des compétitions', 'error');
            } finally {
                this.loading.competitions = false;
            }
        },
        
        // Chargement des engagements du club
        async loadEngagements() {
            this.loading.engagements = true;
            try {
                const response = await fetch('/competitions/api/engagements');
                this.engagements = await response.json();
                console.log('✅ Engagements chargés:', this.engagements.length);
            } catch (error) {
                console.error('❌ Erreur chargement engagements:', error);
                this.showNotification('Erreur lors du chargement des engagements', 'error');
            } finally {
                this.loading.engagements = false;
            }
        },
        
        // Chargement de l'effectif
        async loadEffectif() {
            this.loading.effectif = true;
            try {
                const response = await fetch('/competitions/api/effectif');
                this.effectif = await response.json();
                console.log('✅ Effectif chargé:', this.effectif.length);
            } catch (error) {
                console.error('❌ Erreur chargement effectif:', error);
                this.showNotification('Erreur lors du chargement de l\'effectif', 'error');
            } finally {
                this.loading.effectif = false;
            }
        },
        
        // Vérification de l'effectif
        async verifierEffectif() {
            this.showNotification('Vérification de l\'effectif en cours...', 'info');
            try {
                const response = await fetch('/competitions/api/verifier-effectif', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    this.showNotification('Effectif vérifié avec succès', 'success');
                    this.loadEffectif(); // Recharger l'effectif
                } else {
                    throw new Error('Erreur lors de la vérification');
                }
            } catch (error) {
                console.error('❌ Erreur vérification effectif:', error);
                this.showNotification('Erreur lors de la vérification de l\'effectif', 'error');
            }
        },
        
        // Vérification d'un joueur spécifique
        async verifierJoueur(joueurId) {
            this.showNotification(`Vérification du joueur ${joueurId}...`, 'info');
            try {
                const response = await fetch(`/competitions/api/verifier-joueur/${joueurId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    this.showNotification('Joueur vérifié avec succès', 'success');
                    this.loadEffectif(); // Recharger l'effectif
                } else {
                    throw new Error('Erreur lors de la vérification');
                }
            } catch (error) {
                console.error('❌ Erreur vérification joueur:', error);
                this.showNotification('Erreur lors de la vérification du joueur', 'error');
            }
        },
        
        // Soumission d'une feuille de match
        async soumettreFeuilleMatch(matchId, effectif) {
            this.showNotification('Soumission de la feuille de match...', 'info');
            try {
                const response = await fetch(`/competitions/api/soumettre-feuille/${matchId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ effectif })
                });
                
                if (response.ok) {
                    this.showNotification('Feuille de match soumise avec succès', 'success');
                    this.modals.feuilleMatch = false;
                    this.loadFeuilles(); // Recharger les feuilles
                } else {
                    throw new Error('Erreur lors de la soumission');
                }
            } catch (error) {
                console.error('❌ Erreur soumission feuille:', error);
                this.showNotification('Erreur lors de la soumission de la feuille de match', 'error');
            }
        },
        
        // Reprogrammation d'un match
        async reprogrammerMatch(matchId, nouvelleDate) {
            this.showNotification('Reprogrammation du match...', 'info');
            try {
                const response = await fetch(`/competitions/api/reprogrammer-match/${matchId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ nouvelle_date: nouvelleDate })
                });
                
                if (response.ok) {
                    this.showNotification('Match reprogrammé avec succès', 'success');
                    this.modals.reprogrammerMatch = false;
                    this.loadMatchs(); // Recharger les matchs
                } else {
                    throw new Error('Erreur lors de la reprogrammation');
                }
            } catch (error) {
                console.error('❌ Erreur reprogrammation match:', error);
                this.showNotification('Erreur lors de la reprogrammation du match', 'error');
            }
        },
        
        // Mise à jour d'un résultat
        async mettreAJourResultat(matchId, resultat) {
            this.showNotification('Mise à jour du résultat...', 'info');
            try {
                const response = await fetch(`/competitions/api/mettre-a-jour-resultat/${matchId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ resultat })
                });
                
                if (response.ok) {
                    this.showNotification('Résultat mis à jour avec succès', 'success');
                    this.loadMatchs(); // Recharger les matchs
                } else {
                    throw new Error('Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('❌ Erreur mise à jour résultat:', error);
                this.showNotification('Erreur lors de la mise à jour du résultat', 'error');
            }
        },
        
        // Ajout d'une sanction
        async ajouterSanction(sanction) {
            this.showNotification('Ajout de la sanction...', 'info');
            try {
                const response = await fetch('/competitions/api/ajouter-sanction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(sanction)
                });
                
                if (response.ok) {
                    this.showNotification('Sanction ajoutée avec succès', 'success');
                    this.modals.ajouterSanction = false;
                    this.loadSanctions(); // Recharger les sanctions
                } else {
                    throw new Error('Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('❌ Erreur ajout sanction:', error);
                this.showNotification('Erreur lors de l\'ajout de la sanction', 'error');
            }
        },
        
        // Export d'un rapport
        async exportRapport(type, format) {
            this.showNotification(`Export du rapport ${type}...`, 'info');
            try {
                const response = await fetch(`/competitions/api/export-rapport/${type}?format=${format}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `rapport_${type}_${new Date().toISOString().split('T')[0]}.${format}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showNotification('Rapport exporté avec succès', 'success');
                } else {
                    throw new Error('Erreur lors de l\'export');
                }
            } catch (error) {
                console.error('❌ Erreur export rapport:', error);
                this.showNotification('Erreur lors de l\'export du rapport', 'error');
            }
        },
        
        // Affichage des notifications
        showNotification(message, type = 'info') {
            const notification = {
                id: Date.now(),
                message,
                type,
                timestamp: new Date()
            };
            
            this.notifications.push(notification);
            
            // Auto-suppression après 5 secondes
            setTimeout(() => {
                this.removeNotification(notification.id);
            }, 5000);
        },
        
        // Suppression d'une notification
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        },
        
        // Configuration des écouteurs d'événements
        setupEventListeners() {
            // Écouteur pour les notifications push
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.ready.then(registration => {
                    registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY')
                    }).then(subscription => {
                        console.log('✅ Push subscription activée');
                    });
                });
            }
        },
        
        // Utilitaires
        urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');
            
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        },
        
        // Formatage des dates
        formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR');
        },
        
        // Formatage des heures
        formatTime(time) {
            return new Date(`2000-01-01T${time}`).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    },
    
    computed: {
        // Effectif éligible
        effectifEligible() {
            return this.effectif.filter(joueur => joueur.statut === 'Éligible');
        },
        
        // Effectif inéligible
        effectifIneligible() {
            return this.effectif.filter(joueur => joueur.statut !== 'Éligible');
        },
        
        // Matchs à venir
        matchsAVenir() {
            return this.matchs.filter(match => new Date(match.date) > new Date());
        },
        
        // Matchs joués
        matchsJoues() {
            return this.matchs.filter(match => match.statut === 'Joué');
        },
        
        // Feuilles en attente
        feuillesEnAttente() {
            return this.feuilles.filter(feuille => feuille.statut === 'À préparer');
        },
        
        // Sanctions récentes
        sanctionsRecentes() {
            return this.sanctions.filter(sanction => {
                const dateSanction = new Date(sanction.date);
                const dateLimite = new Date();
                dateLimite.setDate(dateLimite.getDate() - 30); // 30 derniers jours
                return dateSanction > dateLimite;
            });
        }
    }
};

// Initialisation de l'application Vue.js
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('competitions-app')) {
        Vue.createApp(CompetitionsApp).mount('#competitions-app');
        console.log('🏆 Application Vue.js Compétitions montée');
    }
});

// Export pour utilisation dans d'autres modules
window.CompetitionsApp = CompetitionsApp;






