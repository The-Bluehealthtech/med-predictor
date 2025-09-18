"""
Agents spécialisés pour FIT CLI
"""

from .base import BaseAgent
from .patient import PatientAgent
from .clinician import ClinicianAgent
from .ai import AIAgent

__all__ = ['BaseAgent', 'PatientAgent', 'ClinicianAgent', 'AIAgent']
