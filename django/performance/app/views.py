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
        symbol = "1edb3b74-96e9-63f0-89ec-4bbc31885670"
        transactions = Transaction.objects.filter(account=id, symbol=symbol)


        holdings = calculate_holdings(transactions)


        # serialized_transactions = ModelSerializer(transactions).data
        return Response(holdings)

