# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey and OneToOneField has `on_delete` set to the desired behavior
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from django.db import models


class Account(models.Model):
    id = models.UUIDField(primary_key=True)
    exchange = models.ForeignKey('Exchange', models.DO_NOTHING, blank=True, null=True)
    user = models.ForeignKey('User', models.DO_NOTHING)
    private_key = models.CharField(max_length=255, blank=True, null=True)
    public_key = models.CharField(max_length=255, blank=True, null=True)
    synchro = models.TextField(blank=True, null=True)  # This field type is a guess.
    name = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'account'


class AccountAssetReturn(models.Model):
    id = models.BigAutoField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    asset = models.ForeignKey('Asset', models.DO_NOTHING)
    date = models.DateTimeField()
    returnOnInvestment = models.FloatField()

    class Meta:
        managed = False
        db_table = 'account_asset_return'


class Asset(models.Model):
    id = models.CharField(primary_key=True, max_length=255)
    code = models.CharField(max_length=255)
    name = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'asset'


class AssetExchange(models.Model):
    asset = models.OneToOneField(Asset, models.DO_NOTHING, primary_key=True)
    exchange = models.ForeignKey('Exchange', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'asset_exchange'
        unique_together = (('asset', 'exchange'),)


class AssetPrices(models.Model):
    id = models.BigAutoField(primary_key=True)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    timestamp = models.BigIntegerField()
    price = models.FloatField()
    assetPrice = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'asset_prices'


class BinanceAccount(models.Model):
    id = models.UUIDField(primary_key=True)
    private_key = models.CharField(max_length=255, blank=True, null=True)
    public_key = models.CharField(max_length=255, blank=True, null=True)
    synchro = models.TextField(blank=True, null=True)  # This field type is a guess.
    name = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'binance_account'


class Currency(models.Model):
    id = models.UUIDField(primary_key=True)
    code_iso = models.CharField(max_length=255)
    name = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'currency'


class Device(models.Model):
    id = models.UUIDField(primary_key=True)
    user_device = models.ForeignKey('User', models.DO_NOTHING, blank=True, null=True)
    os_version = models.CharField(max_length=255, blank=True, null=True)
    lang = models.CharField(max_length=255, blank=True, null=True)
    os_name = models.CharField(max_length=255, blank=True, null=True)
    device_uuid = models.CharField(max_length=255, blank=True, null=True)
    device_type = models.CharField(max_length=255, blank=True, null=True)
    device_name = models.CharField(max_length=255, blank=True, null=True)
    device_model = models.CharField(max_length=255, blank=True, null=True)
    app_version = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'device'


class DoctrineMigrationVersions(models.Model):
    version = models.CharField(primary_key=True, max_length=191)
    executed_at = models.DateTimeField(blank=True, null=True)
    execution_time = models.IntegerField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'doctrine_migration_versions'


class Exchange(models.Model):
    id = models.UUIDField(primary_key=True)
    label = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'exchange'


class Holding(models.Model):
    id = models.BigAutoField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    quantity = models.FloatField()
    date = models.DateTimeField()
    returnOnInvestment = models.FloatField()

    class Meta:
        managed = False
        db_table = 'holding'


class MessengerMessages(models.Model):
    id = models.BigAutoField(primary_key=True)
    body = models.TextField()
    headers = models.TextField()
    queue_name = models.CharField(max_length=190)
    created_at = models.DateTimeField()
    available_at = models.DateTimeField()
    delivered_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'messenger_messages'


class RefreshToken(models.Model):
    id = models.IntegerField(primary_key=True)
    refresh_token = models.CharField(unique=True, max_length=128)
    username = models.CharField(max_length=255)
    valid = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'refresh_token'


class Symbol(models.Model):
    id = models.UUIDField(primary_key=True)
    code = models.CharField(unique=True, max_length=20)

    class Meta:
        managed = False
        db_table = 'symbol'


class SymbolExchange(models.Model):
    symbol = models.OneToOneField(Symbol, models.DO_NOTHING, primary_key=True)
    exchange = models.ForeignKey(Exchange, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'symbol_exchange'
        unique_together = (('symbol', 'exchange'),)


class Transaction(models.Model):
    id = models.UUIDField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    symbol = models.ForeignKey(Symbol, models.DO_NOTHING)
    asset_fees = models.ForeignKey(Asset, models.DO_NOTHING, blank=True, null=True, related_name='asset_fees_transaction')
    date = models.DateTimeField()
    type = models.CharField(max_length=255)
    order_direction = models.CharField(max_length=255)
    fees = models.FloatField()
    price = models.FloatField()
    quantity = models.FloatField()
    external_transaction_id = models.CharField(max_length=255, blank=True, null=True)
    asset = models.ForeignKey(Asset, models.DO_NOTHING, blank=True, null=True, related_name='asset_transaction')

    class Meta:
        managed = False
        db_table = 'transaction'


class Transfer(models.Model):
    id = models.IntegerField(primary_key=True)
    asset = models.ForeignKey(Asset, models.DO_NOTHING, related_name='asset')
    asset_fees = models.ForeignKey(Asset, models.DO_NOTHING, blank=True, null=True, related_name='asset_fees')
    account = models.ForeignKey(Account, models.DO_NOTHING)
    date = models.DateTimeField()
    quantity = models.FloatField()
    fees = models.FloatField(blank=True, null=True)
    external_transfer_id = models.CharField(max_length=255, blank=True, null=True)
    type = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'transfer'


class User(models.Model):
    id = models.UUIDField(primary_key=True)
    email = models.CharField(unique=True, max_length=180)
    roles = models.TextField()  # This field type is a guess.
    password = models.CharField(max_length=255)
    username = models.CharField(max_length=255, blank=True, null=True)
    birthdate = models.DateField(blank=True, null=True)
    name = models.CharField(max_length=255)
    lastname = models.CharField(max_length=255)
    plain_password = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'user'
