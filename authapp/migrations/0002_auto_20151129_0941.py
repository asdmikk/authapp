# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('authapp', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='person',
            name='fb_name',
            field=models.CharField(max_length=50, default=''),
        ),
        migrations.AddField(
            model_name='person',
            name='gg_name',
            field=models.CharField(max_length=50, default=''),
        ),
    ]
