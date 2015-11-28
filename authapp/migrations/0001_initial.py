# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='Person',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('name', models.CharField(max_length=50)),
                ('id_code', models.CharField(max_length=11)),
                ('email', models.EmailField(max_length=254)),
                ('reg_date', models.DateTimeField(verbose_name='date registered')),
            ],
        ),
    ]
