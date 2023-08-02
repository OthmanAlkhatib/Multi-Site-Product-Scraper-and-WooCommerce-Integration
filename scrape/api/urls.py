from django.urls import path
from .views import GetProduct

urlpatterns = [
    path('get-product', GetProduct.as_view()),
]