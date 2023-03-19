from rest_framework.response import Response
from rest_framework import viewsets
from app.models import Transaction, Holding, Transfer, Asset, AssetPrices, Account
from app.serializers import ModelSerializer
from datetime import datetime, time, timedelta
from app.functions import *
from django.core import serializers
import requests

class HoldingsViewSet(viewsets.ModelViewSet):

    @staticmethod
    def generate_holdings(request, id):
        return_value = []

        # Get all transactions for account and group by asset
        assets = Transaction.objects.filter(account=id).values('asset').distinct()
        for asset in assets:
            # check if asset is not null
            if asset['asset'] is None:
                continue
            asset = asset['asset']
            transactions = Transaction.objects.filter(account=id, asset=asset)
            if transactions.count() == 0:
                continue
            last_holding = Holding.objects.filter(account=id,
                                                  asset=asset).order_by('-date').first()
            # if last_holding is >= yesterday continue
            if last_holding is not None:
                if last_holding.date.date() >= (datetime.today() - timedelta(days=1)).date():
                    continue
            holdings = calculate_holdings(transactions, True)
            return_value.append(holdings)

        return Response(return_value)

