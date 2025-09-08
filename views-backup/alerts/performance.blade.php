@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Performance Alerts</h2>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Alert Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="alertType">Alert Type:</label>
                            <select id="alertType" class="form-control">
                                <option value="">All Types</option>
                                <option value="performance_drop">Performance Drop</option>
                                <option value="injury_risk">Injury Risk</option>
                                <option value="fitness_concern">Fitness Concern</option>
                                <option value="recovery_needed">Recovery Needed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="severity">Severity:</label>
                            <select id="severity" class="form-control">
                                <option value="">All Severities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status">Status:</label>
                            <select id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="acknowledged">Acknowledged</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary form-control" onclick="filterAlerts()">
                                Filter Alerts
                            </button>
                        </div>
                    </div>

                    <!-- Alerts List -->
                    <div class="alerts-container">
                        <!-- Sample Alert Items -->
                        <div class="alert-item alert alert-warning">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Performance Drop Detected
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Player:</strong> John Doe (ID: 12345)<br>
                                        <strong>Issue:</strong> Performance score dropped by 15% over the last 7 days<br>
                                        <strong>Current Score:</strong> 72 (Previous: 87)
                                    </p>
                                    <small class="text-muted">
                                        <strong>Detected:</strong> 2024-01-15 14:30:00<br>
                                        <strong>Severity:</strong> <span class="badge badge-warning">Medium</span>
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="acknowledgeAlert(1)">
                                        Acknowledge
                                    </button>
                                    <button class="btn btn-sm btn-outline-success mb-2" onclick="resolveAlert(1)">
                                        Resolve
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewDetails(1)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert-item alert alert-danger">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-heartbeat"></i>
                                        High Injury Risk Detected
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Player:</strong> Jane Smith (ID: 12346)<br>
                                        <strong>Issue:</strong> Elevated injury risk based on recent performance patterns<br>
                                        <strong>Risk Level:</strong> 85% (Threshold: 70%)
                                    </p>
                                    <small class="text-muted">
                                        <strong>Detected:</strong> 2024-01-15 13:45:00<br>
                                        <strong>Severity:</strong> <span class="badge badge-danger">High</span>
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="acknowledgeAlert(2)">
                                        Acknowledge
                                    </button>
                                    <button class="btn btn-sm btn-outline-success mb-2" onclick="resolveAlert(2)">
                                        Resolve
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewDetails(2)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert-item alert alert-info">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="alert-heading">
                                        <i class="fas fa-running"></i>
                                        Recovery Recommendation
                                    </h5>
                                    <p class="mb-1">
                                        <strong>Player:</strong> Mike Johnson (ID: 12347)<br>
                                        <strong>Issue:</strong> Recommended recovery period based on recent match load<br>
                                        <strong>Recommendation:</strong> 48 hours rest before next training session
                                    </p>
                                    <small class="text-muted">
                                        <strong>Detected:</strong> 2024-01-15 12:15:00<br>
                                        <strong>Severity:</strong> <span class="badge badge-info">Low</span>
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="acknowledgeAlert(3)">
                                        Acknowledge
                                    </button>
                                    <button class="btn btn-sm btn-outline-success mb-2" onclick="resolveAlert(3)">
                                        Resolve
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewDetails(3)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Alerts pagination">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Details Modal -->
<div class="modal fade" id="alertDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="alertDetailsContent">
                    <!-- Alert details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.alert-item {
    margin-bottom: 1rem;
    border-left: 4px solid;
}

.alert-item.alert-warning {
    border-left-color: #ffc107;
}

.alert-item.alert-danger {
    border-left-color: #dc3545;
}

.alert-item.alert-info {
    border-left-color: #17a2b8;
}

.alert-heading {
    margin-bottom: 0.5rem;
}

.alert-heading i {
    margin-right: 0.5rem;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    margin-left: 0.25rem;
}
</style>

<script>
function filterAlerts() {
    const alertType = document.getElementById('alertType').value;
    const severity = document.getElementById('severity').value;
    const status = document.getElementById('status').value;
    
    // Here you would typically make an AJAX call to filter alerts
    console.log('Filtering alerts:', { alertType, severity, status });
    
    // For now, just show a message
    alert('Filter functionality would be implemented here');
}

function acknowledgeAlert(alertId) {
    if (confirm('Are you sure you want to acknowledge this alert?')) {
        // Here you would typically make an AJAX call to acknowledge the alert
        console.log('Acknowledging alert:', alertId);
        
        // For now, just show a message
        alert('Alert acknowledged successfully');
    }
}

function resolveAlert(alertId) {
    if (confirm('Are you sure you want to resolve this alert?')) {
        // Here you would typically make an AJAX call to resolve the alert
        console.log('Resolving alert:', alertId);
        
        // For now, just show a message
        alert('Alert resolved successfully');
    }
}

function viewDetails(alertId) {
    // Here you would typically make an AJAX call to get alert details
    console.log('Viewing details for alert:', alertId);
    
    // For now, just show the modal with sample data
    const modal = document.getElementById('alertDetailsModal');
    const content = document.getElementById('alertDetailsContent');
    
    content.innerHTML = `
        <h6>Alert ID: ${alertId}</h6>
        <p><strong>Type:</strong> Performance Drop</p>
        <p><strong>Player:</strong> John Doe</p>
        <p><strong>Description:</strong> This alert was triggered due to a significant drop in performance metrics over the last week.</p>
        <p><strong>Recommendations:</strong></p>
        <ul>
            <li>Review recent training load</li>
            <li>Check for any injuries or health issues</li>
            <li>Consider adjusting training intensity</li>
            <li>Schedule a medical assessment if needed</li>
        </ul>
    `;
    
    $(modal).modal('show');
}
</script>
@endsection 