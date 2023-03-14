from rest_framework.response import Response
from rest_framework import viewsets
from app.models import Transaction, Holding
from app.serializers import ModelSerializer
from datetime import datetime, time
from app.functions import *
from django.core import serializers
import requests

class HoldingsViewSet(viewsets.ModelViewSet):

    @staticmethod
    def generate_holdings(request, id):
        # Get all account transactions
        symbol = "1edbf3e0-8a8f-6472-af84-a15f96965f13"
        transactions = Transaction.objects.filter(account=id, symbol=symbol)


        holdings = calculate_holdings(transactions)


        # serialized_transactions = ModelSerializer(transactions).data
        return Response(holdings)

