#!/usr/bin/env python3
"""
Script de test pour valider l'installation de FIT CLI
"""

import sys
import os
import subprocess
from pathlib import Path

def test_imports():
    """Tester les imports des modules"""
    print("🔍 Test des imports...")
    
    try:
        from fit_cli.config import Config
        print("✅ Config importé avec succès")
    except ImportError as e:
        print(f"❌ Erreur import Config: {e}")
        return False
    
    try:
        from fit_cli.agents.patient import PatientAgent
        print("✅ PatientAgent importé avec succès")
    except ImportError as e:
        print(f"❌ Erreur import PatientAgent: {e}")
        return False
    
    try:
        from fit_cli.agents.clinician import ClinicianAgent
        print("✅ ClinicianAgent importé avec succès")
    except ImportError as e:
        print(f"❌ Erreur import ClinicianAgent: {e}")
        return False
    
    try:
        from fit_cli.agents.ai import AIAgent
        print("✅ AIAgent importé avec succès")
    except ImportError as e:
        print(f"❌ Erreur import AIAgent: {e}")
        return False
    
    return True

def test_config():
    """Tester la configuration"""
    print("\n🔧 Test de la configuration...")
    
    try:
        from fit_cli.config import Config
        config = Config()
        
        print(f"✅ Configuration chargée: {config.base_url}")
        print(f"✅ Timeout: {config.timeout}s")
        print(f"✅ Rôle par défaut: {config.default_role}")
        
        return True
    except Exception as e:
        print(f"❌ Erreur configuration: {e}")
        return False

def test_agents():
    """Tester les agents"""
    print("\n🤖 Test des agents...")
    
    try:
        from fit_cli.agents.patient import PatientAgent
        from fit_cli.agents.clinician import ClinicianAgent
        from fit_cli.agents.ai import AIAgent
        
        # Test PatientAgent
        patient = PatientAgent()
        print(f"✅ PatientAgent créé (rôle: {patient.role})")
        
        # Test ClinicianAgent
        clinician = ClinicianAgent()
        print(f"✅ ClinicianAgent créé (rôle: {clinician.role})")
        
        # Test AIAgent
        ai = AIAgent()
        print(f"✅ AIAgent créé (rôle: {ai.role})")
        
        return True
    except Exception as e:
        print(f"❌ Erreur agents: {e}")
        return False

def test_cli_executable():
    """Tester l'exécutable CLI"""
    print("\n🚀 Test de l'exécutable CLI...")
    
    try:
        # Vérifier que le fichier existe
        cli_path = Path("fit")
        if not cli_path.exists():
            print("❌ Fichier 'fit' non trouvé")
            return False
        
        # Vérifier les permissions
        if not os.access(cli_path, os.X_OK):
            print("❌ Fichier 'fit' non exécutable")
            return False
        
        print("✅ Fichier 'fit' trouvé et exécutable")
        
        # Tester l'exécution
        result = subprocess.run(["./fit", "--help"], capture_output=True, text=True)
        if result.returncode == 0:
            print("✅ CLI s'exécute correctement")
            return True
        else:
            print(f"❌ Erreur d'exécution CLI: {result.stderr}")
            return False
            
    except Exception as e:
        print(f"❌ Erreur test CLI: {e}")
        return False

def test_dependencies():
    """Tester les dépendances"""
    print("\n📦 Test des dépendances...")
    
    required_packages = [
        'typer',
        'httpx',
        'pydantic',
        'dotenv',
        'rich',
        'click',
        'jwt',
        'cryptography',
        'dateutil',
        'tabulate'
    ]
    
    missing_packages = []
    
    for package in required_packages:
        try:
            __import__(package.replace('-', '_'))
            print(f"✅ {package} installé")
        except ImportError:
            print(f"❌ {package} manquant")
            missing_packages.append(package)
    
    if missing_packages:
        print(f"\n❌ Packages manquants: {', '.join(missing_packages)}")
        print("Installez-les avec: pip install -r requirements.txt")
        return False
    
    return True

def main():
    """Fonction principale de test"""
    print("🧪 Test d'installation FIT CLI")
    print("=" * 50)
    
    tests = [
        ("Dépendances", test_dependencies),
        ("Imports", test_imports),
        ("Configuration", test_config),
        ("Agents", test_agents),
        ("Exécutable CLI", test_cli_executable)
    ]
    
    passed = 0
    total = len(tests)
    
    for test_name, test_func in tests:
        try:
            if test_func():
                passed += 1
            else:
                print(f"\n❌ Test '{test_name}' échoué")
        except Exception as e:
            print(f"\n❌ Test '{test_name}' erreur: {e}")
    
    print("\n" + "=" * 50)
    print(f"📊 Résultats: {passed}/{total} tests réussis")
    
    if passed == total:
        print("🎉 Installation réussie! FIT CLI est prêt à être utilisé.")
        print("\n🚀 Commandes de test:")
        print("  ./fit --help")
        print("  ./fit status")
        print("  ./fit patient symptoms 'test'")
        return 0
    else:
        print("❌ Installation incomplète. Veuillez corriger les erreurs ci-dessus.")
        return 1

if __name__ == "__main__":
    sys.exit(main())
