from django.db import models


class Person(models.Model):
    name = models.CharField(max_length=50)
    id_code = models.CharField(max_length=11)
    email = models.EmailField(max_length=254)
    fb_name = models.CharField(max_length=50, default='')
    gg_name = models.CharField(max_length=50, default='')
    reg_date = models.DateTimeField('date registered')

    def __str__(self):
        return self.name