from rest_framework.response import Response
from rest_framework import viewsets
from app.models import Transaction, Holding, Transfer, Asset, AssetPrices, Account, Prices, Currency
from app.serializers import ModelSerializer
from datetime import datetime, time, timedelta
from app.functions import *
from django.core import serializers
import requests
from pycoingecko import CoinGeckoAPI


class HoldingsViewSet(viewsets.ModelViewSet):

    @staticmethod
    def generate_holdings(request, id):
        return_value = []

        # Get all transactions for account and group by asset
        assets = Transaction.objects.filter(account=id).values('asset').distinct()

        # Unset if asset is null
        assets = [asset for asset in assets if asset['asset'] is not None]

        """
        cg = CoinGeckoAPI()
        currencies = cg.get_supported_vs_currencies()
        for currency in currencies:
            new_currency = Currency.objects.create(name=currency)
        """

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
            holdings = calculate_holdings(transactions)
            return_value.append(holdings)

        return Response(return_value)


    @staticmethod
    def generate_account_returns(request, id):

        assets_account = Holding.objects.filter(account=id).values('asset').distinct()
        assets_account = [asset for asset in assets_account if asset['asset'] is not None]

        account = Account.objects.get(id=id)

        for asset in assets_account:
            asset_object = Asset.objects.get(id=asset['asset'])
            holdings = account_asset_return(account, asset_object)

        return Response('ok')