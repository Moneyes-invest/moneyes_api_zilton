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
    exchange = models.ForeignKey('Exchange', models.DO_NOTHING)
    user = models.ForeignKey('User', models.DO_NOTHING)
    private_key = models.CharField(max_length=255, blank=True, null=True)
    public_key = models.CharField(max_length=255, blank=True, null=True)
    exchange_0 = models.CharField(db_column='exchange', max_length=255)  # Field renamed because of name conflict.

    class Meta:
        managed = False
        db_table = 'account'


class Asset(models.Model):
    id = models.UUIDField(primary_key=True)
    code = models.CharField(unique=True, max_length=3)

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


class AuthGroup(models.Model):
    name = models.CharField(unique=True, max_length=150)

    class Meta:
        managed = False
        db_table = 'auth_group'


class AuthGroupPermissions(models.Model):
    id = models.BigAutoField(primary_key=True)
    group = models.ForeignKey(AuthGroup, models.DO_NOTHING)
    permission = models.ForeignKey('AuthPermission', models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'auth_group_permissions'
        unique_together = (('group', 'permission'),)


class AuthPermission(models.Model):
    name = models.CharField(max_length=255)
    content_type = models.ForeignKey('DjangoContentType', models.DO_NOTHING)
    codename = models.CharField(max_length=100)

    class Meta:
        managed = False
        db_table = 'auth_permission'
        unique_together = (('content_type', 'codename'),)


class AuthUser(models.Model):
    password = models.CharField(max_length=128)
    last_login = models.DateTimeField(blank=True, null=True)
    is_superuser = models.BooleanField()
    username = models.CharField(unique=True, max_length=150)
    first_name = models.CharField(max_length=150)
    last_name = models.CharField(max_length=150)
    email = models.CharField(max_length=254)
    is_staff = models.BooleanField()
    is_active = models.BooleanField()
    date_joined = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'auth_user'


class AuthUserGroups(models.Model):
    id = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(AuthUser, models.DO_NOTHING)
    group = models.ForeignKey(AuthGroup, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'auth_user_groups'
        unique_together = (('user', 'group'),)


class AuthUserUserPermissions(models.Model):
    id = models.BigAutoField(primary_key=True)
    user = models.ForeignKey(AuthUser, models.DO_NOTHING)
    permission = models.ForeignKey(AuthPermission, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'auth_user_user_permissions'
        unique_together = (('user', 'permission'),)


class BinanceAccount(models.Model):
    id = models.OneToOneField(Account, models.DO_NOTHING, db_column='id', primary_key=True)

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


class DjangoAdminLog(models.Model):
    action_time = models.DateTimeField()
    object_id = models.TextField(blank=True, null=True)
    object_repr = models.CharField(max_length=200)
    action_flag = models.SmallIntegerField()
    change_message = models.TextField()
    content_type = models.ForeignKey('DjangoContentType', models.DO_NOTHING, blank=True, null=True)
    user = models.ForeignKey(AuthUser, models.DO_NOTHING)

    class Meta:
        managed = False
        db_table = 'django_admin_log'


class DjangoContentType(models.Model):
    app_label = models.CharField(max_length=100)
    model = models.CharField(max_length=100)

    class Meta:
        managed = False
        db_table = 'django_content_type'
        unique_together = (('app_label', 'model'),)


class DjangoMigrations(models.Model):
    id = models.BigAutoField(primary_key=True)
    app = models.CharField(max_length=255)
    name = models.CharField(max_length=255)
    applied = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'django_migrations'


class DjangoSession(models.Model):
    session_key = models.CharField(primary_key=True, max_length=40)
    session_data = models.TextField()
    expire_date = models.DateTimeField()

    class Meta:
        managed = False
        db_table = 'django_session'


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
    id = models.IntegerField(primary_key=True)
    account = models.ForeignKey(Account, models.DO_NOTHING)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    quantity = models.FloatField()
    date = models.DateTimeField()

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
    asset_fees = models.ForeignKey(Asset, models.DO_NOTHING, blank=True, null=True)
    date = models.DateTimeField()
    type = models.CharField(max_length=255)
    order_direction = models.CharField(max_length=255)
    fees = models.FloatField()
    price = models.FloatField()
    quantity = models.FloatField()
    external_transaction_id = models.CharField(max_length=255, blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'transaction'


class Transfer(models.Model):
    id = models.IntegerField(primary_key=True)
    asset = models.ForeignKey(Asset, models.DO_NOTHING)
    asset_fees = models.ForeignKey(Asset, models.DO_NOTHING, blank=True, null=True)
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
