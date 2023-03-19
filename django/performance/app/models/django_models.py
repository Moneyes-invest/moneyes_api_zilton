from django.db import models
from . import *

class Holding(models.Model):
    id = models.BigAutoField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    quantity = models.FloatField()
    date = models.DateTimeField()
    return_on_investment = models.FloatField(blank=True, null=True)

    class Meta:
        db_table = 'holding'


class AssetPrices(models.Model):
    id = models.BigAutoField(primary_key=True)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    timestamp = models.BigIntegerField() # Unix timestamp in milliseconds
    price = models.FloatField()
    asset_price = models.CharField(max_length=255, default='USD')

    class Meta:
        db_table = 'asset_prices'


class AccountAssetReturn(models.Model):
    id = models.BigAutoField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    date = models.DateTimeField()
    return_on_investment = models.FloatField(blank=True, null=True)

    class Meta:
        db_table = 'account_asset_return'