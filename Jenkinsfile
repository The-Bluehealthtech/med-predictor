pipeline {
    agent any
    
    environment {
        PHP_VERSION = '8.2'
        COMPOSER_CACHE_DIR = "${WORKSPACE}/.composer-cache"
        AUDIT_REPORT_DIR = "${WORKSPACE}/audit-reports"
    }
    
    options {
        timeout(time: 30, unit: 'MINUTES')
        timestamps()
        ansiColor('xterm')
    }
    
    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo "🔍 Début de l'audit de base de données"
            }
        }
        
        stage('Setup Environment') {
            steps {
                script {
                    // Créer le répertoire de cache Composer
                    sh "mkdir -p ${COMPOSER_CACHE_DIR}"
                    
                    // Installer PHP et dépendances
                    sh '''
                        # Installer PHP et extensions
                        sudo apt-get update -qq
                        sudo apt-get install -y -qq php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-zip unzip sqlite3 jq
                        
                        # Installer Composer
                        if [ ! -f /usr/local/bin/composer ]; then
                            curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
                        fi
                        
                        # Configurer Composer
                        composer config cache-dir ${COMPOSER_CACHE_DIR}
                    '''
                }
            }
        }
        
        stage('Install Dependencies') {
            steps {
                sh '''
                    # Installer les dépendances PHP
                    composer install --prefer-dist --no-progress --no-interaction
                    
                    # Copier et configurer l'environnement
                    cp .env.example .env
                    php artisan key:generate
                    
                    # Créer la base SQLite
                    touch database/database.sqlite
                    echo "DB_CONNECTION=sqlite" >> .env
                    echo "DB_DATABASE=database/database.sqlite" >> .env
                    
                    # Exécuter les migrations
                    php artisan migrate --force
                '''
            }
        }
        
        stage('Run Database Audit') {
            steps {
                script {
                    // Créer le répertoire de rapports
                    sh "mkdir -p ${AUDIT_REPORT_DIR}"
                    
                    // Exécuter l'audit
                    sh '''
                        echo "🔍 Exécution de l'audit de base de données..."
                        php artisan project:db:audit
                        
                        # Trouver le rapport le plus récent
                        LATEST_REPORT=$(ls -t storage/logs/db-audit-*.log | head -1)
                        
                        if [ -z "$LATEST_REPORT" ]; then
                            echo "❌ Aucun rapport d'audit trouvé"
                            exit 1
                        fi
                        
                        echo "📋 Rapport trouvé: $LATEST_REPORT"
                        
                        # Copier le rapport dans le répertoire de travail
                        cp "$LATEST_REPORT" "${AUDIT_REPORT_DIR}/latest-audit.json"
                        
                        # Extraire les métriques clés
                        CLEANLINESS_SCORE=$(grep -o '"cleanliness_score": "[^"]*"' "$LATEST_REPORT" | cut -d'"' -f4 | sed 's/%//')
                        TOTAL_TABLES=$(grep -o '"total_tables": [0-9]*' "$LATEST_REPORT" | cut -d':' -f2 | tr -d ' ')
                        ORPHAN_TABLES=$(grep -o '"orphan_tables": \[[^]]*\]' "$LATEST_REPORT" | grep -o '"[^"]*"' | wc -l)
                        UNUSED_MODELS=$(grep -o '"unused_models": \[[^]]*\]' "$LATEST_REPORT" | grep -o '"[^"]*"' | wc -l)
                        
                        # Calculer le pourcentage de tables orphelines
                        ORPHAN_TABLES_PERCENT=$((ORPHAN_TABLES * 100 / TOTAL_TABLES))
                        
                        # Sauvegarder les métriques
                        echo "CLEANLINESS_SCORE=$CLEANLINESS_SCORE" > "${AUDIT_REPORT_DIR}/metrics.env"
                        echo "TOTAL_TABLES=$TOTAL_TABLES" >> "${AUDIT_REPORT_DIR}/metrics.env"
                        echo "ORPHAN_TABLES=$ORPHAN_TABLES" >> "${AUDIT_REPORT_DIR}/metrics.env"
                        echo "UNUSED_MODELS=$UNUSED_MODELS" >> "${AUDIT_REPORT_DIR}/metrics.env"
                        echo "ORPHAN_TABLES_PERCENT=$ORPHAN_TABLES_PERCENT" >> "${AUDIT_REPORT_DIR}/metrics.env"
                        
                        # Afficher le résumé
                        echo "📊 Résultats de l'audit:"
                        echo "Score de propreté: ${CLEANLINESS_SCORE}%"
                        echo "Tables totales: ${TOTAL_TABLES}"
                        echo "Tables orphelines: ${ORPHAN_TABLES}"
                        echo "Modèles non utilisés: ${UNUSED_MODELS}"
                        echo "Pourcentage tables orphelines: ${ORPHAN_TABLES_PERCENT}%"
                    '''
                }
            }
        }
        
        stage('Generate HTML Report') {
            steps {
                script {
                    sh '''
                        # Charger les métriques
                        source "${AUDIT_REPORT_DIR}/metrics.env"
                        
                        # Créer un rapport HTML
                        cat > "${AUDIT_REPORT_DIR}/audit-report.html" << 'EOF'
                        <!DOCTYPE html>
                        <html lang="fr">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Rapport d'Audit Base de Données - Jenkins</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
                                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; text-align: center; }
                                .score { font-size: 3em; font-weight: bold; margin: 20px 0; }
                                .good { color: #28a745; }
                                .warning { color: #ffc107; }
                                .danger { color: #dc3545; }
                                .metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin: 30px 0; }
                                .metric { background: #f8f9fa; padding: 25px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; border-left: 5px solid #007bff; }
                                .recommendations { background: #e7f3ff; padding: 25px; border-radius: 10px; margin: 30px 0; border-left: 5px solid #17a2b8; }
                                .jenkins-info { background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 5px solid #ffc107; }
                                .chart { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
                                .progress-bar { width: 100%; height: 30px; background: #e9ecef; border-radius: 15px; overflow: hidden; }
                                .progress-fill { height: 100%; background: linear-gradient(90deg, #28a745, #20c997); transition: width 0.3s ease; }
                                .progress-fill.warning { background: linear-gradient(90deg, #ffc107, #fd7e14); }
                                .progress-fill.danger { background: linear-gradient(90deg, #dc3545, #e83e8c); }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="header">
                                    <h1>🔍 Rapport d'Audit Base de Données</h1>
                                    <p>Généré par Jenkins Pipeline</p>
                                    <p>Build: ${BUILD_NUMBER} | Job: ${JOB_NAME}</p>
                                </div>
                                
                                <div class="jenkins-info">
                                    <h3>📋 Informations Jenkins</h3>
                                    <p><strong>Build:</strong> #${BUILD_NUMBER}</p>
                                    <p><strong>Job:</strong> ${JOB_NAME}</p>
                                    <p><strong>Branche:</strong> ${GIT_BRANCH}</p>
                                    <p><strong>Commit:</strong> ${GIT_COMMIT}</p>
                                    <p><strong>Date:</strong> ${BUILD_TIMESTAMP}</p>
                                </div>
                                
                                <div class="chart">
                                    <h3>📊 Score de Propreté</h3>
                                    <div class="score $([ $CLEANLINESS_SCORE -ge 80 ] && echo 'good' || ([ $CLEANLINESS_SCORE -ge 70 ] && echo 'warning') || echo 'danger')">
                                        ${CLEANLINESS_SCORE}%
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill $([ $CLEANLINESS_SCORE -ge 80 ] && echo '' || ([ $CLEANLINESS_SCORE -ge 70 ] && echo 'warning') || echo 'danger')" style="width: ${CLEANLINESS_SCORE}%"></div>
                                    </div>
                                </div>
                                
                                <div class="metrics">
                                    <div class="metric">
                                        <h3>🗃️ Tables Totales</h3>
                                        <div class="score">${TOTAL_TABLES}</div>
                                        <p>Tables dans la base de données</p>
                                    </div>
                                    
                                    <div class="metric">
                                        <h3>❌ Tables Orphelines</h3>
                                        <div class="score $([ $ORPHAN_TABLES -eq 0 ] && echo 'good' || ([ $ORPHAN_TABLES -le 5 ] && echo 'warning') || echo 'danger')">
                                            ${ORPHAN_TABLES}
                                        </div>
                                        <p>Sans modèle Eloquent</p>
                                    </div>
                                    
                                    <div class="metric">
                                        <h3>🏗️ Modèles Non Utilisés</h3>
                                        <div class="score $([ $UNUSED_MODELS -eq 0 ] && echo 'good' || ([ $UNUSED_MODELS -le 3 ] && echo 'warning') || echo 'danger')">
                                            ${UNUSED_MODELS}
                                        </div>
                                        <p>Jamais référencés</p>
                                    </div>
                                    
                                    <div class="metric">
                                        <h3>📈 Pourcentage Orphelines</h3>
                                        <div class="score $([ $ORPHAN_TABLES_PERCENT -le 10 ] && echo 'good' || ([ $ORPHAN_TABLES_PERCENT -le 20 ] && echo 'warning') || echo 'danger')">
                                            ${ORPHAN_TABLES_PERCENT}%
                                        </div>
                                        <p>Tables sans modèle</p>
                                    </div>
                                </div>
                                
                                <div class="recommendations">
                                    <h3>💡 Recommandations</h3>
                                    <ul>
                    '''
                    
                    // Ajouter des recommandations dynamiques
                    sh '''
                        source "${AUDIT_REPORT_DIR}/metrics.env"
                        
                        if [ "$CLEANLINESS_SCORE" -lt 80 ]; then
                            echo "                                        <li>Le score de propreté est bas (${CLEANLINESS_SCORE}%). Considérez nettoyer le code avant de merger.</li>" >> "${AUDIT_REPORT_DIR}/audit-report.html"
                        fi
                        
                        if [ "$ORPHAN_TABLES_PERCENT" -gt 20 ]; then
                            echo "                                        <li>Trop de tables orphelines (${ORPHAN_TABLES_PERCENT}%). Créez des modèles ou supprimez les tables inutiles.</li>" >> "${AUDIT_REPORT_DIR}/audit-report.html"
                        fi
                        
                        if [ "$UNUSED_MODELS" -gt 10 ]; then
                            echo "                                        <li>Trop de modèles non utilisés (${UNUSED_MODELS}). Vérifiez leur utilité.</li>" >> "${AUDIT_REPORT_DIR}/audit-report.html"
                        fi
                        
                        # Fermer le HTML
                        cat >> "${AUDIT_REPORT_DIR}/audit-report.html" << 'EOF'
                                    </ul>
                                </div>
                                
                                <div>
                                    <h3>📋 Rapport Complet</h3>
                                    <p>Le rapport JSON complet est disponible dans les artifacts de ce build Jenkins.</p>
                                    <p><a href="artifact/latest-audit.json" target="_blank">Télécharger le rapport JSON</a></p>
                                </div>
                            </div>
                        </body>
                        </html>
                    EOF
                    '''
                    
                    echo "📄 Rapport HTML généré avec succès"
                }
            }
        }
        
        stage('Quality Gate') {
            steps {
                script {
                    sh '''
                        source "${AUDIT_REPORT_DIR}/metrics.env"
                        
                        echo "🔍 Vérification de la qualité..."
                        
                        # Vérifier le score de propreté
                        if [ "$CLEANLINESS_SCORE" -lt 70 ]; then
                            echo "❌ Score de propreté critique: ${CLEANLINESS_SCORE}%"
                            echo "Le pipeline échouera pour maintenir la qualité du code"
                            currentBuild.result = 'FAILURE'
                            error("Score de propreté trop bas: ${CLEANLINESS_SCORE}%")
                        fi
                        
                        # Vérifier le pourcentage de tables orphelines
                        if [ "$ORPHAN_TABLES_PERCENT" -gt 30 ]; then
                            echo "❌ Trop de tables orphelines: ${ORPHAN_TABLES_PERCENT}%"
                            echo "Le pipeline échouera pour maintenir la qualité du code"
                            currentBuild.result = 'FAILURE'
                            error("Trop de tables orphelines: ${ORPHAN_TABLES_PERCENT}%")
                        fi
                        
                        # Vérifier les modèles non utilisés
                        if [ "$UNUSED_MODELS" -gt 15 ]; then
                            echo "❌ Trop de modèles non utilisés: ${UNUSED_MODELS}"
                            echo "Le pipeline échouera pour maintenir la qualité du code"
                            currentBuild.result = 'FAILURE'
                            error("Trop de modèles non utilisés: ${UNUSED_MODELS}")
                        fi
                        
                        echo "✅ Tous les critères de qualité sont respectés"
                        echo "📊 Score de propreté: ${CLEANLINESS_SCORE}%"
                        echo "🗃️ Tables orphelines: ${ORPHAN_TABLES} (${ORPHAN_TABLES_PERCENT}%)"
                        echo "🏗️ Modèles non utilisés: ${UNUSED_MODELS}"
                    '''
                }
            }
        }
        
        stage('Archive Reports') {
            steps {
                script {
                    // Archiver les rapports
                    archiveArtifacts artifacts: "${AUDIT_REPORT_DIR}/**/*", fingerprint: true
                    
                    // Publier le rapport HTML
                    publishHTML([
                        allowMissing: false,
                        alwaysLinkToLastBuild: true,
                        keepAll: true,
                        reportDir: AUDIT_REPORT_DIR,
                        reportFiles: 'audit-report.html',
                        reportName: 'Database Audit Report',
                        reportTitles: 'Rapport d\'Audit Base de Données'
                    ])
                }
            }
        }
        
        stage('Notify Team') {
            when {
                anyOf {
                    branch 'main'
                    branch 'master'
                    branch 'develop'
                }
            }
            steps {
                script {
                    sh '''
                        source "${AUDIT_REPORT_DIR}/metrics.env"
                        
                        # Préparer le message de notification
                        if [ "$CLEANLINESS_SCORE" -lt 80 ]; then
                            EMOJI="⚠️"
                            STATUS="Attention"
                            COLOR="#ffc107"
                        elif [ "$CLEANLINESS_SCORE" -lt 90 ]; then
                            EMOJI="📊"
                            STATUS="Bon"
                            COLOR="#17a2b8"
                        else
                            EMOJI="🎉"
                            STATUS="Excellent"
                            COLOR="#28a745"
                        fi
                        
                        echo "Notification préparée:"
                        echo "Status: $STATUS"
                        echo "Score: ${CLEANLINESS_SCORE}%"
                        echo "Tables orphelines: ${ORPHAN_TABLES}"
                        echo "Modèles non utilisés: ${UNUSED_MODELS}"
                        
                        # Ici vous pouvez ajouter l'intégration avec Slack, Teams, etc.
                        # Exemple pour Slack (nécessite SLACK_WEBHOOK_URL dans les variables Jenkins)
                        if [ -n "$SLACK_WEBHOOK_URL" ]; then
                            curl -X POST -H 'Content-type: application/json' \
                                 --data "{\"text\":\"$EMOJI *Audit Base de Données* - $STATUS\nScore: ${CLEANLINESS_SCORE}%\nTables orphelines: ${ORPHAN_TABLES}\nModèles non utilisés: ${UNUSED_MODELS}\nBuild: ${BUILD_URL}\"}" \
                                 "$SLACK_WEBHOOK_URL"
                        fi
                    '''
                }
            }
        }
    }
    
    post {
        always {
            script {
                // Nettoyer les fichiers temporaires
                sh "rm -rf ${COMPOSER_CACHE_DIR}"
                
                // Sauvegarder les métriques pour les builds suivants
                if (fileExists("${AUDIT_REPORT_DIR}/metrics.env")) {
                    sh '''
                        source "${AUDIT_REPORT_DIR}/metrics.env"
                        echo "CLEANLINESS_SCORE=${CLEANLINESS_SCORE}" > "${WORKSPACE}/last-audit-metrics.txt"
                        echo "TOTAL_TABLES=${TOTAL_TABLES}" >> "${WORKSPACE}/last-audit-metrics.txt"
                        echo "ORPHAN_TABLES=${ORPHAN_TABLES}" >> "${WORKSPACE}/last-audit-metrics.txt"
                        echo "UNUSED_MODELS=${UNUSED_MODELS}" >> "${WORKSPACE}/last-audit-metrics.txt"
                        echo "ORPHAN_TABLES_PERCENT=${ORPHAN_TABLES_PERCENT}" >> "${WORKSPACE}/last-audit-metrics.txt"
                    '''
                    
                    archiveArtifacts artifacts: 'last-audit-metrics.txt', fingerprint: true
                }
            }
        }
        
        success {
            echo "🎉 Audit de base de données terminé avec succès !"
        }
        
        failure {
            echo "❌ Audit de base de données a échoué !"
        }
        
        unstable {
            echo "⚠️  Audit de base de données instable - vérifiez les résultats"
        }
    }
}
