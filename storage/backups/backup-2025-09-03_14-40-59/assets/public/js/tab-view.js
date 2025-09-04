// Composant JavaScript simple pour les onglets
class TabView {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            defaultTab: options.defaultTab || null,
            onTabChange: options.onTabChange || null,
            ...options
        };
        
        this.activeTab = this.options.defaultTab;
        this.tabs = [];
        this.init();
    }
    
    init() {
        if (!this.container) {
            console.error('TabView: Container not found');
            return;
        }
        
        // Trouver les onglets dans le conteneur
        this.findTabs();
        
        // Initialiser le premier onglet actif
        if (this.tabs.length > 0 && !this.activeTab) {
            this.activeTab = this.tabs[0].id;
        }
        
        // Appliquer l'état initial
        this.updateTabs();
        
        // Ajouter les écouteurs d'événements
        this.addEventListeners();
    }
    
    findTabs() {
        // Chercher les boutons d'onglets
        const tabButtons = this.container.querySelectorAll('.tab-button');
        
        tabButtons.forEach((button, index) => {
            const tabId = button.getAttribute('data-tab') || button.getAttribute('id') || `tab-${index}`;
            const tabLabel = button.textContent.trim();
            const tabIcon = button.querySelector('.tab-icon')?.textContent || '';
            
            this.tabs.push({
                id: tabId,
                label: tabLabel,
                icon: tabIcon,
                button: button,
                content: this.findTabContent(tabId)
            });
        });
    }
    
    findTabContent(tabId) {
        // Chercher le contenu correspondant à l'onglet
        return this.container.querySelector(`[data-tab-content="${tabId}"]`) || 
               this.container.querySelector(`#${tabId}-content`) ||
               this.container.querySelector(`[data-tab="${tabId}"]`);
    }
    
    addEventListeners() {
        this.tabs.forEach(tab => {
            tab.button.addEventListener('click', () => {
                this.setActiveTab(tab.id);
            });
        });
    }
    
    setActiveTab(tabId) {
        if (this.activeTab === tabId) return;
        
        this.activeTab = tabId;
        this.updateTabs();
        
        // Appeler le callback si défini
        if (this.options.onTabChange) {
            this.options.onTabChange(tabId);
        }
        
        // Émettre un événement personnalisé
        this.container.dispatchEvent(new CustomEvent('tab-change', {
            detail: { activeTab: tabId }
        }));
    }
    
    updateTabs() {
        this.tabs.forEach(tab => {
            const isActive = tab.id === this.activeTab;
            
            // Mettre à jour le bouton
            if (isActive) {
                tab.button.classList.add('active');
                tab.button.classList.remove('inactive');
            } else {
                tab.button.classList.remove('active');
                tab.button.classList.add('inactive');
            }
            
            // Mettre à jour le contenu
            if (tab.content) {
                if (isActive) {
                    tab.content.style.display = 'block';
                    tab.content.classList.add('active');
                    tab.content.classList.remove('inactive');
                } else {
                    tab.content.style.display = 'none';
                    tab.content.classList.remove('active');
                    tab.content.classList.add('inactive');
                }
            }
        });
    }
    
    // Méthodes publiques
    getActiveTab() {
        return this.activeTab;
    }
    
    getTabs() {
        return this.tabs;
    }
    
    addTab(tabConfig) {
        // Ajouter un nouvel onglet dynamiquement
        const newTab = {
            id: tabConfig.id,
            label: tabConfig.label,
            icon: tabConfig.icon || '',
            button: null,
            content: null
        };
        
        this.tabs.push(newTab);
        this.updateTabs();
    }
    
    removeTab(tabId) {
        const index = this.tabs.findIndex(tab => tab.id === tabId);
        if (index > -1) {
            this.tabs.splice(index, 1);
            
            // Si l'onglet supprimé était actif, activer le premier disponible
            if (this.activeTab === tabId && this.tabs.length > 0) {
                this.setActiveTab(this.tabs[0].id);
            }
            
            this.updateTabs();
        }
    }
}

// Exposer globalement pour utilisation dans les vues Blade
window.TabView = TabView;

