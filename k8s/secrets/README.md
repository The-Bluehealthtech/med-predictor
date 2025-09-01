# Generated Secrets Summary

Generated on: Mer 27 aoû 2025 08:23:57 CET

## Database Credentials
- Username: med_user
- Password: 6It2jPEG9rTnuszimu4kQfX++ZaueUU1WV0XqxKDfAM=
- Root Password: 40ERRKyLJTTy6CV2FxPaweA85eOobYjrqZgKcA35IF8=

## Redis Credentials
- Password: dxzqV2qc1VVqjvWRLJzjNoeFZTM2SFkAoQBMMgbQauA=

## Application Keys
- Laravel App Key: e2eHEdkL8SLO2Tvza61bU01Hu4gxRgDQXI4XO8m6IXQ=
- JWT Secret: QE8GfzQrUl8c3tZU06DNlrF21Do47uTmb/9WBE2Sz0/9pA+WsQAKPAAdNCOF5gJd
0XQT8AOyUQlPDCQLZtdg9Q==
- Session Key: 5sjJtLpdCKGXz0alF33bsfqJB+zIhLfSFriKPeUdI58=

## Mail Credentials
- Password: nrJkLX7ohpg1A9gFAy7TzWZCuiR9KcyMYweelafbEBU=

## Grafana Credentials
- Admin Password: r26NA449i2wmws9DtRLpasqEAlNhpDDUXTradXbhc0E=

## Important Notes
1. These secrets are generated automatically and are secure
2. Store this file securely and do not commit to version control
3. Use these credentials for your Kubernetes deployment
4. For production, consider using external secret management solutions

## Usage
```bash
# Apply secrets to Kubernetes
kubectl apply -f k8s/secrets.yaml

# Check if secrets are created
kubectl get secrets -n med-predictor
```
