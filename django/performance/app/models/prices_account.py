from pycoingecko import CoinGeckoAPI
from datetime import datetime, timedelta
from app.models import AssetPrices, Asset
from django.db import connection
from django.core import serializers
import json
import requests
from datetime import datetime

class Prices:

    asset_list = [] # List of uuid of assets
    cg = CoinGeckoAPI()

    def __init__(self, asset_list):
        self.asset_list = asset_list

    def start(self):
        for asset in self.asset_list:
            try:
                price = AssetPrices.objects.filter(asset=asset).order_by('-timestamp').first()
                if price is None:
                    self.fetch_prices(asset)
                    continue
                # Update price if older than 24 hours
                date_today = int(datetime.now().timestamp() * 1000)
                last_update = price.timestamp
                one_day_timestamp_ms = 86400000
                # Check if last_update is older than today 00:00:00 UTC
                today_utc_00_00_00 = int(datetime.combine(datetime.today(), datetime.min.time()).timestamp() * 1000)
                is_up_to_date = last_update > today_utc_00_00_00
                if not is_up_to_date:
                    date = (date_today - last_update) / one_day_timestamp_ms
                    date = int(date) + 1
                    self.fetch_prices(asset, date=date)
            except AssetPrices.DoesNotExist:
                self.fetch_prices(asset)
            continue

    def fetch_prices(self, asset, date=None):
        if date is None:
            date = 'max'
        else:
            date = date
        prices = self.cg.get_coin_market_chart_by_id(id=asset.id, vs_currency='eur', days=date)
        prices = prices['prices']
        for price in prices:
            # check if price already exist
            try:
                AssetPrices.objects.get(asset=asset, timestamp=price[0])
            except AssetPrices.DoesNotExist:
                new_price = AssetPrices.objects.create(asset=asset, timestamp=price[0], price=price[1], asset_price='EUR')


    def get_price(self, asset, date):
        try:
            price = AssetPrices.objects.filter(asset=asset, timestamp=date).first()
        except AssetPrices.DoesNotExist:
            return 0
        if price is None:
            return 0
        return price.price