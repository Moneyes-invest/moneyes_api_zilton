from rest_framework import serializers
from app.models import Transaction

class ModelSerializer(serializers.ModelSerializer):
	class Meta:
		model = Transaction
		fields = ('account', 'symbol', 'date')