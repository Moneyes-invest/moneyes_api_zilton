from django.contrib import admin
from django.urls import path
from rest_framework.routers import DefaultRouter
from app.views import HoldingsViewSet

router = DefaultRouter()

urlpatterns = [
    path('admin/', admin.site.urls),
    path('accounts/<str:id>/holdings', HoldingsViewSet.as_view({'post': 'generate_holdings'})),
]