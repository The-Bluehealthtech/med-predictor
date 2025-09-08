#!/bin/bash

# Google Cloud Storage Backup Setup Script
# This script sets up automated backups for the Med-Predictor application

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
STORAGE_BUCKET="${GOOGLE_CLOUD_STORAGE_BUCKET:-med-predictor-storage}"
RETENTION_DAYS="${GCS_BACKUP_RETENTION_DAYS:-30}"

echo -e "${BLUE}🔄 Google Cloud Storage Backup Setup${NC}"
echo "====================================="

# Create backup script
create_backup_script() {
    echo -e "\n${YELLOW}📝 Creating backup script...${NC}"
    
    cat > scripts/gcs-backup.sh << 'EOF'
#!/bin/bash

# GCS Backup Script for Med-Predictor
# This script creates automated backups of critical files

set -e

# Configuration
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
STORAGE_BUCKET="${GOOGLE_CLOUD_STORAGE_BUCKET:-med-predictor-storage}"
RETENTION_DAYS="${GCS_BACKUP_RETENTION_DAYS:-30}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}🔄 Starting GCS backup process...${NC}"

# Set up authentication
export GOOGLE_APPLICATION_CREDENTIALS="${GOOGLE_CLOUD_KEY_FILE:-/path/to/service-account-key.json}"

# Create timestamp
TIMESTAMP=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_PATH="backups/${TIMESTAMP}"

echo "Backup timestamp: ${TIMESTAMP}"
echo "Backup path: gs://${BACKUP_BUCKET}/${BACKUP_PATH}"

# Function to backup directory
backup_directory() {
    local source_path="$1"
    local backup_name="$2"
    
    echo "Backing up ${backup_name}..."
    
    if gsutil -m cp -r "gs://${STORAGE_BUCKET}/${source_path}" "gs://${BACKUP_BUCKET}/${BACKUP_PATH}/${backup_name}/"; then
        echo -e "${GREEN}✅ ${backup_name} backed up successfully${NC}"
    else
        echo -e "${RED}❌ Failed to backup ${backup_name}${NC}"
        return 1
    fi
}

# Function to backup specific files
backup_files() {
    local file_pattern="$1"
    local backup_name="$2"
    
    echo "Backing up files matching ${file_pattern}..."
    
    # Create temporary directory
    TEMP_DIR=$(mktemp -d)
    
    # Download files matching pattern
    if gsutil -m cp "gs://${STORAGE_BUCKET}/${file_pattern}" "${TEMP_DIR}/"; then
        # Upload to backup location
        if gsutil -m cp -r "${TEMP_DIR}" "gs://${BACKUP_BUCKET}/${BACKUP_PATH}/${backup_name}/"; then
            echo -e "${GREEN}✅ ${backup_name} files backed up successfully${NC}"
        else
            echo -e "${RED}❌ Failed to upload ${backup_name} backup${NC}"
            rm -rf "${TEMP_DIR}"
            return 1
        fi
    else
        echo -e "${RED}❌ Failed to download ${backup_name} files${NC}"
        rm -rf "${TEMP_DIR}"
        return 1
    fi
    
    # Clean up temporary directory
    rm -rf "${TEMP_DIR}"
}

# Create backup manifest
create_backup_manifest() {
    echo "Creating backup manifest..."
    
    cat > /tmp/backup-manifest.json << EOF
{
    "timestamp": "${TIMESTAMP}",
    "project_id": "${PROJECT_ID}",
    "source_bucket": "${STORAGE_BUCKET}",
    "backup_bucket": "${BACKUP_BUCKET}",
    "backup_path": "${BACKUP_PATH}",
    "retention_days": ${RETENTION_DAYS},
    "backup_type": "automated",
    "components": [
        "uploads",
        "documents",
        "images",
        "exports"
    ]
}
EOF
    
    # Upload manifest
    gsutil cp /tmp/backup-manifest.json "gs://${BACKUP_BUCKET}/${BACKUP_PATH}/backup-manifest.json"
    rm /tmp/backup-manifest.json
    
    echo -e "${GREEN}✅ Backup manifest created${NC}"
}

# Main backup process
main() {
    echo "Starting backup process..."
    
    # Backup critical directories
    backup_directory "uploads" "uploads" || echo "Warning: Uploads backup failed"
    backup_directory "documents" "documents" || echo "Warning: Documents backup failed"
    backup_directory "images" "images" || echo "Warning: Images backup failed"
    backup_directory "exports" "exports" || echo "Warning: Exports backup failed"
    
    # Backup specific file types
    backup_files "*.pdf" "pdf-files" || echo "Warning: PDF backup failed"
    backup_files "*.jpg" "jpg-files" || echo "Warning: JPG backup failed"
    backup_files "*.png" "png-files" || echo "Warning: PNG backup failed"
    
    # Create backup manifest
    create_backup_manifest
    
    echo -e "${GREEN}🎉 Backup process completed successfully!${NC}"
    echo "Backup location: gs://${BACKUP_BUCKET}/${BACKUP_PATH}"
}

# Cleanup old backups
cleanup_old_backups() {
    echo -e "\n${YELLOW}🧹 Cleaning up old backups...${NC}"
    
    # Calculate cutoff date
    CUTOFF_DATE=$(date -d "${RETENTION_DAYS} days ago" +%Y-%m-%d)
    echo "Removing backups older than: ${CUTOFF_DATE}"
    
    # List and delete old backup directories
    gsutil ls "gs://${BACKUP_BUCKET}/backups/" | while read backup_dir; do
        # Extract date from directory name
        dir_name=$(basename "$backup_dir")
        if [[ $dir_name =~ ^([0-9]{4}-[0-9]{2}-[0-9]{2})_([0-9]{2}-[0-9]{2}-[0-9]{2})$ ]]; then
            backup_date=${BASH_REMATCH[1]}
            if [[ "$backup_date" < "$CUTOFF_DATE" ]]; then
                echo "Deleting old backup: ${backup_dir}"
                gsutil -m rm -r "$backup_dir"
            fi
        fi
    done
    
    echo -e "${GREEN}✅ Old backups cleaned up${NC}"
}

# Run main function
main
cleanup_old_backups

echo -e "${GREEN}🔄 Backup process completed!${NC}"
EOF
    
    chmod +x scripts/gcs-backup.sh
    echo -e "${GREEN}✅ Backup script created: scripts/gcs-backup.sh${NC}"
}

# Create restore script
create_restore_script() {
    echo -e "\n${YELLOW}📝 Creating restore script...${NC}"
    
    cat > scripts/gcs-restore.sh << 'EOF'
#!/bin/bash

# GCS Restore Script for Med-Predictor
# This script restores files from GCS backups

set -e

# Configuration
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
STORAGE_BUCKET="${GOOGLE_CLOUD_STORAGE_BUCKET:-med-predictor-storage}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔄 GCS Restore Script${NC}"
echo "====================="

# Function to list available backups
list_backups() {
    echo -e "\n${YELLOW}📋 Available backups:${NC}"
    
    gsutil ls "gs://${BACKUP_BUCKET}/backups/" | while read backup_dir; do
        dir_name=$(basename "$backup_dir")
        if [[ $dir_name =~ ^([0-9]{4}-[0-9]{2}-[0-9]{2})_([0-9]{2}-[0-9]{2}-[0-9]{2})$ ]]; then
            echo "  - ${dir_name}"
        fi
    done
}

# Function to restore from backup
restore_backup() {
    local backup_timestamp="$1"
    local component="$2"
    
    if [ -z "$backup_timestamp" ]; then
        echo -e "${RED}❌ Backup timestamp required${NC}"
        echo "Usage: $0 restore <timestamp> [component]"
        echo "Example: $0 restore 2024-01-01_12-00-00 uploads"
        exit 1
    fi
    
    local backup_path="backups/${backup_timestamp}"
    
    echo -e "${YELLOW}🔄 Restoring from backup: ${backup_timestamp}${NC}"
    
    if [ -n "$component" ]; then
        # Restore specific component
        echo "Restoring component: ${component}"
        if gsutil -m cp -r "gs://${BACKUP_BUCKET}/${backup_path}/${component}" "gs://${STORAGE_BUCKET}/"; then
            echo -e "${GREEN}✅ Component ${component} restored successfully${NC}"
        else
            echo -e "${RED}❌ Failed to restore component ${component}${NC}"
            exit 1
        fi
    else
        # Restore all components
        echo "Restoring all components..."
        if gsutil -m cp -r "gs://${BACKUP_BUCKET}/${backup_path}" "gs://${STORAGE_BUCKET}/restored-${backup_timestamp}/"; then
            echo -e "${GREEN}✅ All components restored successfully${NC}"
        else
            echo -e "${RED}❌ Failed to restore backup${NC}"
            exit 1
        fi
    fi
}

# Function to show backup details
show_backup_details() {
    local backup_timestamp="$1"
    
    if [ -z "$backup_timestamp" ]; then
        echo -e "${RED}❌ Backup timestamp required${NC}"
        exit 1
    fi
    
    local backup_path="backups/${backup_timestamp}"
    
    echo -e "${YELLOW}📋 Backup Details: ${backup_timestamp}${NC}"
    echo "================================"
    
    # Show backup manifest if exists
    if gsutil ls "gs://${BACKUP_BUCKET}/${backup_path}/backup-manifest.json" &> /dev/null; then
        echo "Backup Manifest:"
        gsutil cat "gs://${BACKUP_BUCKET}/${backup_path}/backup-manifest.json" | jq .
    fi
    
    # List backup contents
    echo -e "\nBackup Contents:"
    gsutil ls -r "gs://${BACKUP_BUCKET}/${backup_path}/"
}

# Main function
main() {
    case "$1" in
        "list")
            list_backups
            ;;
        "restore")
            restore_backup "$2" "$3"
            ;;
        "details")
            show_backup_details "$2"
            ;;
        *)
            echo -e "${YELLOW}Usage: $0 {list|restore|details} [options]${NC}"
            echo ""
            echo "Commands:"
            echo "  list                           - List available backups"
            echo "  restore <timestamp> [component] - Restore from backup"
            echo "  details <timestamp>            - Show backup details"
            echo ""
            echo "Examples:"
            echo "  $0 list"
            echo "  $0 restore 2024-01-01_12-00-00"
            echo "  $0 restore 2024-01-01_12-00-00 uploads"
            echo "  $0 details 2024-01-01_12-00-00"
            exit 1
            ;;
    esac
}

# Set up authentication
export GOOGLE_APPLICATION_CREDENTIALS="${GOOGLE_CLOUD_KEY_FILE:-/path/to/service-account-key.json}"

# Run main function
main "$@"
EOF
    
    chmod +x scripts/gcs-restore.sh
    echo -e "${GREEN}✅ Restore script created: scripts/gcs-restore.sh${NC}"
}

# Create cron job configuration
create_cron_config() {
    echo -e "\n${YELLOW}⏰ Creating cron job configuration...${NC}"
    
    cat > scripts/setup-backup-cron.sh << 'EOF'
#!/bin/bash

# Setup Cron Job for GCS Backups
# This script sets up automated backup scheduling

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}⏰ Setting up GCS backup cron job...${NC}"

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_SCRIPT="${SCRIPT_DIR}/gcs-backup.sh"

# Check if backup script exists
if [ ! -f "$BACKUP_SCRIPT" ]; then
    echo -e "${RED}❌ Backup script not found: $BACKUP_SCRIPT${NC}"
    exit 1
fi

# Create cron job entry
CRON_ENTRY="0 2 * * * ${BACKUP_SCRIPT} >> /var/log/gcs-backup.log 2>&1"

echo "Cron job entry:"
echo "$CRON_ENTRY"

# Add to crontab
(crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -

echo -e "${GREEN}✅ Cron job added successfully${NC}"
echo "Backups will run daily at 2:00 AM"
echo "Logs will be written to: /var/log/gcs-backup.log"
EOF
    
    chmod +x scripts/setup-backup-cron.sh
    echo -e "${GREEN}✅ Cron setup script created: scripts/setup-backup-cron.sh${NC}"
}

# Create backup monitoring script
create_monitoring_script() {
    echo -e "\n${YELLOW}📊 Creating backup monitoring script...${NC}"
    
    cat > scripts/gcs-backup-monitor.sh << 'EOF'
#!/bin/bash

# GCS Backup Monitoring Script
# This script monitors backup status and sends alerts

set -e

# Configuration
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
BACKUP_BUCKET="${GCS_BACKUP_BUCKET:-med-predictor-backups}"
ALERT_EMAIL="${BACKUP_ALERT_EMAIL:-admin@med-predictor.com}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}📊 GCS Backup Monitor${NC}"
echo "===================="

# Function to check last backup
check_last_backup() {
    echo "Checking last backup..."
    
    # Get the most recent backup
    LAST_BACKUP=$(gsutil ls "gs://${BACKUP_BUCKET}/backups/" | sort -r | head -1)
    
    if [ -z "$LAST_BACKUP" ]; then
        echo -e "${RED}❌ No backups found${NC}"
        return 1
    fi
    
    # Extract timestamp from backup path
    BACKUP_TIMESTAMP=$(basename "$LAST_BACKUP")
    echo "Last backup: ${BACKUP_TIMESTAMP}"
    
    # Check if backup is recent (within 25 hours)
    BACKUP_DATE=$(echo "$BACKUP_TIMESTAMP" | cut -d'_' -f1)
    BACKUP_TIME=$(echo "$BACKUP_TIMESTAMP" | cut -d'_' -f2)
    
    BACKUP_DATETIME="${BACKUP_DATE} ${BACKUP_TIME//-/:}"
    BACKUP_EPOCH=$(date -d "$BACKUP_DATETIME" +%s)
    CURRENT_EPOCH=$(date +%s)
    HOURS_DIFF=$(( (CURRENT_EPOCH - BACKUP_EPOCH) / 3600 ))
    
    if [ $HOURS_DIFF -gt 25 ]; then
        echo -e "${RED}❌ Last backup is ${HOURS_DIFF} hours old${NC}"
        return 1
    else
        echo -e "${GREEN}✅ Last backup is ${HOURS_DIFF} hours old${NC}"
        return 0
    fi
}

# Function to check backup size
check_backup_size() {
    echo "Checking backup sizes..."
    
    # Get total backup size
    TOTAL_SIZE=$(gsutil du -s "gs://${BACKUP_BUCKET}/backups/" | awk '{print $1}')
    TOTAL_SIZE_MB=$(( TOTAL_SIZE / 1024 / 1024 ))
    
    echo "Total backup size: ${TOTAL_SIZE_MB} MB"
    
    # Check if size is reasonable (less than 10GB)
    if [ $TOTAL_SIZE_MB -gt 10240 ]; then
        echo -e "${YELLOW}⚠️  Backup size is large: ${TOTAL_SIZE_MB} MB${NC}"
    else
        echo -e "${GREEN}✅ Backup size is reasonable: ${TOTAL_SIZE_MB} MB${NC}"
    fi
}

# Function to check backup integrity
check_backup_integrity() {
    echo "Checking backup integrity..."
    
    # Get the most recent backup
    LAST_BACKUP=$(gsutil ls "gs://${BACKUP_BUCKET}/backups/" | sort -r | head -1)
    BACKUP_PATH=$(basename "$LAST_BACKUP")
    
    # Check if manifest exists
    if gsutil ls "gs://${BACKUP_BUCKET}/backups/${BACKUP_PATH}/backup-manifest.json" &> /dev/null; then
        echo -e "${GREEN}✅ Backup manifest exists${NC}"
        
        # Validate manifest
        MANIFEST_CONTENT=$(gsutil cat "gs://${BACKUP_BUCKET}/backups/${BACKUP_PATH}/backup-manifest.json")
        if echo "$MANIFEST_CONTENT" | jq . &> /dev/null; then
            echo -e "${GREEN}✅ Backup manifest is valid JSON${NC}"
        else
            echo -e "${RED}❌ Backup manifest is invalid JSON${NC}"
            return 1
        fi
    else
        echo -e "${RED}❌ Backup manifest missing${NC}"
        return 1
    fi
}

# Function to send alert
send_alert() {
    local message="$1"
    local subject="GCS Backup Alert - $(date)"
    
    echo "Sending alert: $message"
    
    # Send email (requires mail command)
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "$subject" "$ALERT_EMAIL"
        echo -e "${GREEN}✅ Alert sent to ${ALERT_EMAIL}${NC}"
    else
        echo -e "${YELLOW}⚠️  Mail command not available, alert not sent${NC}"
    fi
}

# Main monitoring function
main() {
    echo "Starting backup monitoring..."
    
    local issues=0
    
    # Check last backup
    if ! check_last_backup; then
        send_alert "WARNING: Last backup is older than 25 hours"
        ((issues++))
    fi
    
    # Check backup size
    check_backup_size
    
    # Check backup integrity
    if ! check_backup_integrity; then
        send_alert "ERROR: Backup integrity check failed"
        ((issues++))
    fi
    
    # Summary
    if [ $issues -eq 0 ]; then
        echo -e "${GREEN}🎉 All backup checks passed${NC}"
    else
        echo -e "${RED}❌ ${issues} backup issues detected${NC}"
        exit 1
    fi
}

# Run main function
main "$@"
EOF
    
    chmod +x scripts/gcs-backup-monitor.sh
    echo -e "${GREEN}✅ Monitoring script created: scripts/gcs-backup-monitor.sh${NC}"
}

# Main setup function
main() {
    echo -e "${BLUE}Setting up GCS backup system...${NC}"
    
    create_backup_script
    create_restore_script
    create_cron_config
    create_monitoring_script
    
    echo -e "\n${GREEN}🎉 GCS backup system setup completed!${NC}"
    echo -e "${BLUE}Available scripts:${NC}"
    echo -e "${YELLOW}  - scripts/gcs-backup.sh${NC}        - Create backups"
    echo -e "${YELLOW}  - scripts/gcs-restore.sh${NC}        - Restore from backups"
    echo -e "${YELLOW}  - scripts/setup-backup-cron.sh${NC} - Setup automated backups"
    echo -e "${YELLOW}  - scripts/gcs-backup-monitor.sh${NC} - Monitor backup status"
    echo ""
    echo -e "${BLUE}Next steps:${NC}"
    echo -e "${YELLOW}1. Run: ./scripts/setup-backup-cron.sh${NC}"
    echo -e "${YELLOW}2. Test: ./scripts/gcs-backup.sh${NC}"
    echo -e "${YELLOW}3. Monitor: ./scripts/gcs-backup-monitor.sh${NC}"
}

# Run main function
main "$@"

