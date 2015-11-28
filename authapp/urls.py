from django.conf.urls import url

from . import views


urlpatterns = [
    url(r'^$', views.index, name='index'),
    url(r'login/$', views.login, name='login'),
    url(r'persons/$', views.persons, name='persons'),
    url(r'person/(?P<person_id>[0-9]+)/', views.person, name='person')
]