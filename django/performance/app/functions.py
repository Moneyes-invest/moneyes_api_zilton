from datetime import datetime, time
from app.models import Transaction, Holding, Transfer, Asset, AssetPrices
import pandas as pd
from pycoingecko import CoinGeckoAPI
import requests
from django.db import connection




def calculate_holdings(transactions: object) -> object:
    # Order by date DESC
    transactions = transactions.order_by('-date')
    # Get first transaction
    first_transaction = transactions.last()
    # Get date of first transaction
    date = first_transaction.date
    # Get start of day timestamp
    start = int(datetime.combine(date, time.min).timestamp() * 1000)
    # Get end of day timestamp
    end = int(datetime.combine(date, time.max).timestamp() * 1000)

    return_value = []

    index = 0
    price = 0
    VEd = 0

    # Loop through all days
    while True:
        # start timestamp ms to date
        start_date = datetime.fromtimestamp(start / 1000)
        # end timestamp ms to date
        end_date = datetime.fromtimestamp(end / 1000)

        # get all transfers
        asset = "1edbf3e3-603e-638c-92fa-e3fc836ff955"
        asset = Asset.objects.get(id=asset)
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
        try:
            THdm1 = Holding.objects.filter(date=end_date_last_day).last()
            if THdm1 is None:
                THdm1 = 0
            else:
                THdm1 = THdm1.quantity
        except Holding.DoesNotExist:
            THdm1 = 0

        # Calculate Total Holdings Day
        THd = THdm1 + Sqn + Sqm

        # ---- Get Price ----
        # end to timestamp seconds
        price = get_price(asset, end)


        PEd = price
        PEdm1 = price


        # Calculate Value Start Day = Value End Day -1
        VSd = VEd
        if VSd == 0:
            if day_transactions.last() is not None:
                VSd = day_transactions.last().quantity * day_transactions.last().price
            else:
                VSd = 0


        VEd =  THd * PEd




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
            Wn = ( 8640000 - transaction.date.timestamp() * 1000 ) / 8640000
            Sn += Cn * Wn

        # Sum Cash-flow * weight for all transfers
        Sm = 0
        for transfer in day_transfers:
            Cm = transfer.quantity * price
            Wm = ( 8640000 - transfer.date.timestamp() * 1000 ) / 8640000
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

        return_value.append(str(start_date) + " => " + str(VSd) + " : "+ str(VEd) + " : " + str(Cd) + " : "  + str(r) + "%")


        # create_holdings(day_transactions)
        # Get next day
        start = start + 86400000  # 24 hours in milliseconds
        end = end + 86400000  # 24 hours in milliseconds
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
        with connection.cursor() as cursor:
            cursor.execute(
                """
                INSERT INTO holding (date, account_id, quantity, asset_id, return_on_investment)
                VALUES (%s, %s, %s, %s, %s)
                """,
                (date, account_id, quantity, asset_id, return_on_investment)
            )

def get_price(asset: object, timestamp: int) -> float:
    # Check if price exists in asset_prices
    timestamp_seconds = int(timestamp / 1000)

    try:
        asset_price = AssetPrices.objects.get(asset=asset, timestamp=timestamp)
        return asset_price.price
    except AssetPrices.DoesNotExist:
        asset_symbol = asset.code
        response = requests.get(
            f'https://min-api.cryptocompare.com/data/v2/histohour?fsym={asset_symbol}&tsym=USD&limit=1&toTs={timestamp_seconds}')
        data = response.json()
        if data['Response'] == 'Error':
            return 0
        else:
            price = data['Data']['Data'][0]['close']
            # Save price in asset_prices
            asset_price = AssetPrices(asset=asset, timestamp=timestamp, price=price)
            asset_price.save()
            return price



