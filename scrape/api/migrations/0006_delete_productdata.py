# Generated by Django 4.0.6 on 2022-09-26 14:19

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('api', '0005_alter_productdata_attributes_and_more'),
    ]

    operations = [
        migrations.DeleteModel(
            name='ProductData',
        ),
    ]