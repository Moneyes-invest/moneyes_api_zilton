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
        return_value = []
        # Get all symbols for account
        symbols = Transaction.objects.filter(account=id).values('symbol').distinct()

        # For each symbol get all transactions
        for symbol in symbols:
            symbol = symbol['symbol']
            transactions = Transaction.objects.filter(account=id, symbol=symbol)
            holdings = calculate_holdings(transactions)
            return_value.append(holdings)



        # serialized_transactions = ModelSerializer(transactions).data
        return Response(return_value)

