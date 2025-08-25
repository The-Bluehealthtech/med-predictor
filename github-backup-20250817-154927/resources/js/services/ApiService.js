/**
 * Comprehensive API Service for Med Predictor
 * Handles all API communication with proper authentication, caching, and error handling
 */

class ApiService {
    constructor() {
        this.baseURL = '/api/v1';
        this.token = localStorage.getItem('auth_token');
        this.refreshToken = localStorage.getItem('refresh_token');
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        this.retryAttempts = 3;
        this.retryDelay = 1000; // 1 second
    }

    /**
     * Set authentication token
     */
    setToken(token, refreshToken = null) {
        this.token = token;
        this.refreshToken = refreshToken;
        localStorage.setItem('auth_token', token);
        if (refreshToken) {
            localStorage.setItem('refresh_token', refreshToken);
        }
    }

    /**
     * Clear authentication
     */
    clearAuth() {
        this.token = null;
        this.refreshToken = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('refresh_token');
    }

    /**
     * Get default headers
     */
    getHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    /**
     * Make API request with retry logic and token refresh
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            method: 'GET',
            headers: this.getHeaders(),
            ...options
        };

        // Merge headers
        config.headers = { ...this.getHeaders(), ...options.headers };

        let lastError;
        
        for (let attempt = 1; attempt <= this.retryAttempts; attempt++) {
            try {
                const response = await fetch(url, config);
                
                // Handle token refresh
                if (response.status === 401 && this.refreshToken && attempt === 1) {
                    const refreshSuccess = await this.refreshAuthToken();
                    if (refreshSuccess) {
                        config.headers['Authorization'] = `Bearer ${this.token}`;
                        continue;
                    }
                }

                // Handle response
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `HTTP ${response.status}`);
                }

                const data = await response.json();
                return { success: true, data: data.data || data };

            } catch (error) {
                lastError = error;
                
                if (attempt === this.retryAttempts) {
                    break;
                }
                
                // Wait before retry
                await new Promise(resolve => setTimeout(resolve, this.retryDelay * attempt));
            }
        }

        return { success: false, error: lastError.message };
    }

    /**
     * Refresh authentication token
     */
    async refreshAuthToken() {
        try {
            const response = await fetch(`${this.baseURL}/auth/refresh`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.refreshToken}`
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.setToken(data.data.token, data.data.refresh_token);
                return true;
            }
        } catch (error) {
            console.error('Token refresh failed:', error);
        }

        this.clearAuth();
        window.location.href = '/login';
        return false;
    }

    /**
     * Cache management
     */
    getCacheKey(endpoint, params = {}) {
        return `${endpoint}?${new URLSearchParams(params).toString()}`;
    }

    getFromCache(key) {
        const cached = this.cache.get(key);
        if (cached && Date.now() - cached.timestamp < this.cacheTimeout) {
            return cached.data;
        }
        this.cache.delete(key);
        return null;
    }

    setCache(key, data) {
        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
    }

    clearCache(pattern = null) {
        if (pattern) {
            for (const key of this.cache.keys()) {
                if (key.includes(pattern)) {
                    this.cache.delete(key);
                }
            }
        } else {
            this.cache.clear();
        }
    }

    // ==================== AUTHENTICATION ====================

    /**
     * Login user
     */
    async login(credentials) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify(credentials)
        });

        if (response.success) {
            this.setToken(response.data.token, response.data.refresh_token);
        }

        return response;
    }

    /**
     * Logout user
     */
    async logout() {
        const response = await this.request('/auth/logout', {
            method: 'POST'
        });

        this.clearAuth();
        this.clearCache();
        return response;
    }

    /**
     * Get user profile
     */
    async getProfile() {
        return await this.request('/auth/profile');
    }

    // ==================== MATCHES ====================

    /**
     * Get matches with filtering and pagination
     */
    async getMatches(params = {}) {
        const cacheKey = this.getCacheKey('/matches', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/matches?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Get single match
     */
    async getMatch(id) {
        const cacheKey = `/matches/${id}`;
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const response = await this.request(`/matches/${id}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Create match
     */
    async createMatch(matchData) {
        const response = await this.request('/matches', {
            method: 'POST',
            body: JSON.stringify(matchData)
        });

        if (response.success) {
            this.clearCache('matches');
        }

        return response;
    }

    /**
     * Update match
     */
    async updateMatch(id, matchData) {
        const response = await this.request(`/matches/${id}`, {
            method: 'PUT',
            body: JSON.stringify(matchData)
        });

        if (response.success) {
            this.clearCache('matches');
            this.cache.delete(`/matches/${id}`);
        }

        return response;
    }

    /**
     * Delete match
     */
    async deleteMatch(id) {
        const response = await this.request(`/matches/${id}`, {
            method: 'DELETE'
        });

        if (response.success) {
            this.clearCache('matches');
            this.cache.delete(`/matches/${id}`);
        }

        return response;
    }

    // ==================== MATCH EVENTS ====================

    /**
     * Get match events
     */
    async getMatchEvents(params = {}) {
        const cacheKey = this.getCacheKey('/match-events', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/match-events?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Create match event
     */
    async createMatchEvent(eventData) {
        const response = await this.request('/match-events', {
            method: 'POST',
            body: JSON.stringify(eventData)
        });

        if (response.success) {
            this.clearCache('match-events');
        }

        return response;
    }

    /**
     * Update match event
     */
    async updateMatchEvent(id, eventData) {
        const response = await this.request(`/match-events/${id}`, {
            method: 'PUT',
            body: JSON.stringify(eventData)
        });

        if (response.success) {
            this.clearCache('match-events');
        }

        return response;
    }

    /**
     * Delete match event
     */
    async deleteMatchEvent(id) {
        const response = await this.request(`/match-events/${id}`, {
            method: 'DELETE'
        });

        if (response.success) {
            this.clearCache('match-events');
        }

        return response;
    }

    // ==================== MATCH SHEETS ====================

    /**
     * Get match sheets
     */
    async getMatchSheets(params = {}) {
        const cacheKey = this.getCacheKey('/match-sheets', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/match-sheets?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Create match sheet
     */
    async createMatchSheet(sheetData) {
        const response = await this.request('/match-sheets', {
            method: 'POST',
            body: JSON.stringify(sheetData)
        });

        if (response.success) {
            this.clearCache('match-sheets');
        }

        return response;
    }

    /**
     * Update match sheet
     */
    async updateMatchSheet(id, sheetData) {
        const response = await this.request(`/match-sheets/${id}`, {
            method: 'PUT',
            body: JSON.stringify(sheetData)
        });

        if (response.success) {
            this.clearCache('match-sheets');
        }

        return response;
    }

    /**
     * Submit match sheet
     */
    async submitMatchSheet(id) {
        const response = await this.request(`/match-sheets/${id}/submit`, {
            method: 'POST'
        });

        if (response.success) {
            this.clearCache('match-sheets');
        }

        return response;
    }

    // ==================== CLUBS ====================

    /**
     * Get clubs
     */
    async getClubs(params = {}) {
        const cacheKey = this.getCacheKey('/clubs', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/clubs?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Create club
     */
    async createClub(clubData) {
        const response = await this.request('/clubs', {
            method: 'POST',
            body: JSON.stringify(clubData)
        });

        if (response.success) {
            this.clearCache('clubs');
        }

        return response;
    }

    // ==================== COMPETITIONS ====================

    /**
     * Get competitions
     */
    async getCompetitions(params = {}) {
        const cacheKey = this.getCacheKey('/competitions', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/competitions?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Get competition standings
     */
    async getCompetitionStandings(competitionId) {
        const cacheKey = `/competitions/${competitionId}/standings`;
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const response = await this.request(`/competitions/${competitionId}/standings`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    // ==================== PLAYERS ====================

    /**
     * Get players
     */
    async getPlayers(params = {}) {
        const cacheKey = this.getCacheKey('/players', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/players?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Get player profile
     */
    async getPlayerProfile(id) {
        const cacheKey = `/players/${id}/profile`;
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const response = await this.request(`/players/${id}/profile`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    // ==================== SEASONS ====================

    /**
     * Get seasons
     */
    async getSeasons(params = {}) {
        const cacheKey = this.getCacheKey('/seasons', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/seasons?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Get current season
     */
    async getCurrentSeason() {
        const cacheKey = '/seasons/current';
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const response = await this.request('/seasons/current');
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    // ==================== PLAYER LICENSES ====================

    /**
     * Get player licenses
     */
    async getPlayerLicenses(params = {}) {
        const cacheKey = this.getCacheKey('/player-licenses', params);
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return { success: true, data: cached };
        }

        const queryString = new URLSearchParams(params).toString();
        const response = await this.request(`/player-licenses?${queryString}`);
        
        if (response.success) {
            this.setCache(cacheKey, response.data);
        }

        return response;
    }

    /**
     * Approve license
     */
    async approveLicense(id, reason = '') {
        const response = await this.request(`/player-licenses/${id}/approve`, {
            method: 'POST',
            body: JSON.stringify({ reason })
        });

        if (response.success) {
            this.clearCache('player-licenses');
        }

        return response;
    }

    /**
     * Reject license
     */
    async rejectLicense(id, reason) {
        const response = await this.request(`/player-licenses/${id}/reject`, {
            method: 'POST',
            body: JSON.stringify({ reason })
        });

        if (response.success) {
            this.clearCache('player-licenses');
        }

        return response;
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Upload file
     */
    async uploadFile(file, endpoint) {
        const formData = new FormData();
        formData.append('file', file);

        const response = await fetch(`${this.baseURL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Upload failed: ${response.status}`);
        }

        return await response.json();
    }

    /**
     * Export data
     */
    async exportData(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`${this.baseURL}${endpoint}?${queryString}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Export failed: ${response.status}`);
        }

        return await response.blob();
    }
}

// Create singleton instance
const apiService = new ApiService();

// Export for use in components
export default apiService; 