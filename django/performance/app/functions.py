from datetime import datetime, time
from app.models import Transaction, Holding, Transfer, Asset, AssetPrices, AccountAssetReturn, Prices
import pandas as pd
from pycoingecko import CoinGeckoAPI
import requests
from django.db import connection
import json
from django.core import serializers


# function test with update parameter boolean default to false

def calculate_holdings(transactions: object, update=False) -> object:
    # Order by date DESC
    transactions = transactions.order_by('-date')
    # Get first transaction
    first_transaction = transactions.last()

    # check if last holding exists

    last_holding = Holding.objects.filter(account=transactions[0].account, asset=transactions[0].asset).order_by('-date').first()

    # If update is false
    if not update or last_holding is None:
        # Get date of first transaction
        date = first_transaction.date
        # Get start of day timestamp
        start = int(datetime.combine(date, time.min).timestamp() * 1000)
        # Get end of day timestamp
        end = int(datetime.combine(date, time.max).timestamp() * 1000)
        VEd = 0
    else:
        # Get last holding
        date = last_holding.date
        date_time = int(datetime.combine(date, time.max).timestamp() * 1000)
        date_time = date_time - 3600000
        # Get start of day timestamp
        start = int(datetime.combine(date, time.min).timestamp() * 1000)
        # Get end of day timestamp
        end = int(datetime.combine(date, time.max).timestamp() * 1000)
        VEd = last_holding.quantity * get_price(last_holding.asset, date_time)

    return_value = []

    index = 0
    price = 0
    previous_return = 0.0000001
    THd = 0


    # Find asset with symbol

    asset = transactions[0].asset

    # Loop through all days
    while True:
        # start timestamp ms to date
        start_date = datetime.fromtimestamp(start / 1000)
        # end timestamp ms to date
        end_date = datetime.fromtimestamp(end / 1000)

        if start_date.date() == datetime.today().date():
            break

        # get all transfers
        day_transfers = Transfer.objects.filter(date__gte=start_date, date__lte=end_date, asset=asset.id)

        # Get all transactions between start and end date
        day_transactions = transactions.filter(date__gte=start_date, date__lte=end_date)
        # Store queryset day_transactions into holdings array if not empty

        # Calculate return value for this day

        # Calculate Sum of all day_transactions quantity
        Sqn = 0
        for transaction in day_transactions:
            Sqn += transaction.quantity

        # Calculate Sum of all day_transfers quantity
        Sqm = 0
        for transfer in day_transfers:
            if transfer.type == "SELL":
                Sqm -= transfer.quantity
            else:
                Sqm += transfer.quantity

        # Calculate Total Holdings last day
        end_date_last_day = end - 86400000
        end_date_last_day = datetime.fromtimestamp(end_date_last_day / 1000)


        THdm1 = THd
        THd = THdm1 + Sqn + Sqm

        # ---- Get Price ----
        # end to timestamp seconds
        price = get_price(asset, end)

        PEd = price

        # Calculate Value Start Day = Value End Day -1
        VSd = VEd
        """
        if VSd == 0:
            if day_transactions.last() is not None:
                VSd = day_transactions.last().quantity * day_transactions.last().price
            else:
                VSd = 0
        """
        VEd = THd * PEd

        # ---- Calculate Cash-flow for this day ----
        # Cash-flow day sum transactions quantity * price
        Cdn = 0
        for transaction in day_transactions:
            Cdn += transaction.quantity * transaction.price

        # Cash-flow day sum transfers quantity * price
        Cdm = 0
        for transfer in day_transfers:
            Cdm += transfer.quantity * price

        # Cash-flow day
        Cd = Cdn + Cdm

        # ---- Calculate Sum Cash-flow * weight for all transfers and transactions ----
        # Sum Cash-flow * weight for all transactions
        Sn = 0
        for transaction in day_transactions:
            Cn = transaction.quantity * transaction.price
            Wn = (8640000 - transaction.date.timestamp() * 1000) / 8640000
            Sn += Cn * Wn

        # Sum Cash-flow * weight for all transfers
        Sm = 0
        for transfer in day_transfers:
            Cm = transfer.quantity * price
            Wm = (8640000 - transfer.date.timestamp() * 1000) / 8640000
            Sm += Cm * Wm

        # ---- Calculate Return Value ----
        numerator = VEd - VSd - Cd
        denominator = VSd + Sn + Sm
        if denominator == 0:
            r = 0
        else:
            r = numerator / denominator

        # Add to return value
        r = r * 100

        # Create new holding
        save_holding(end_date, first_transaction.account.id, THd, asset.id, r)

        return_value.append(
            asset.code + " -- "+ str(price) + " -- " + str(end_date) + " => VSD " + str(VSd) + " | VED : " + str(VEd) + " : " + str(Cd) + " : " + str(r) + "%")


        # Save account asset return
        # if r == 0:
        #    r = 0.0000001

        # account_asset_return( first_transaction.account, asset, end_date, r, previous_return)


        # create_holdings(day_transactions)
        # Get next day
        start = start + 86400000  # 24 hours in milliseconds
        end = end + 86400000  # 24 hours in milliseconds
        previous_return = r
        # Break if start is greater than now
        if start > int(datetime.now().timestamp() * 1000):
            break

    return return_value


def save_holding(date, account_id, quantity, asset_id, return_on_investment):
    # Check if holding exists
    try:
        holding = Holding.objects.get(date=date, account_id=account_id, asset_id=asset_id)
    except Holding.DoesNotExist:
        # Create new holding
        new_holding = Holding(date=date, account_id=account_id, quantity=quantity, asset_id=asset_id, returnOnInvestment=return_on_investment)
        new_holding.save()

def get_price(asset: object, timestamp: int) -> float:
    # Check if price exists in asset_prices
    timestamp = timestamp + 1 # Convert 23:59:59 to 00:00:00 (for CoinGecko)



    price = Prices([asset])
    price.start()
    return price.get_price(asset, timestamp)

def account_asset_return(account: object, asset: object):

    # Get all holdings for this account and asset
    holdings = Holding.objects.filter(account=account, asset=asset).order_by('date')

    return_asset_account = 1
    # get first holding.return_on_investment
    return_holding = holdings.first().returnOnInvestment

    for holding in holdings:
        # Check if it is the last holding
        if holding == holdings.last():
            break
        next_holding = holdings.filter(date__gt=holding.date).first()
        next_holding = next_holding.returnOnInvestment
        if return_holding == 0:
            return_holding = 0.0000001
        return_asset_account = (((return_holding + next_holding) / 100 ) + ((return_holding / 100) * (next_holding / 100))) * 100
        return_holding = next_holding


    account_asset_return = AccountAssetReturn(account=account, asset=asset, returnOnInvestment=return_asset_account, date=holdings.last().date)
    account_asset_return.save()

    return account_asset_return
