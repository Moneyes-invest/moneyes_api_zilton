from pycoingecko import CoinGeckoAPI
from datetime import datetime, timedelta
from app.models import AssetPrices, Asset
from django.db import connection
from django.core import serializers
import json
import requests

class Prices:

    asset_list = [] # List of uuid of assets
    cg = CoinGeckoAPI()

    def __init__(self, asset_list):
        self.asset_list = asset_list

    def start(self):
        for asset in self.asset_list:
            try:
                price = AssetPrices.objects.filter(asset=asset).order_by('-date').first()
                # Update price if older than 24 hours
            except AssetPrices.DoesNotExist:
                self.fetch_price(asset)

    def fetch_price(self, asset, date=None):
        if date is None:
            date = 'max'
        else:
            date = (datetime.today() - date).days
        prices = cg.get_coin_market_chart_by_id(id=asset.id, vs_currency='eur', days=date)
        prices = prices['prices']
        for price in prices:
            # check if price already exist
            try:
                AssetPrices.objects.get(asset=asset, timestamp=price[0])
            except AssetPrices.DoesNotExist:
                new_price = AssetPrices.objects.create(asset=asset, timestamp=price[0], price=price[1], asset_price='EUR')
