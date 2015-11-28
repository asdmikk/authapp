from django.http import HttpResponse
from django.shortcuts import render, get_object_or_404
from .models import Person


def index(request):
    return HttpResponse("hello woorld!")


def login(request):
    return HttpResponse("Welcome to login")


def persons(request):
    persons_list = Person.objects.all()
    context = {
        'persons_list': persons_list,
    }
    return render(request, 'authapp/persons.html', context)


def person(request, person_id):
    person = get_object_or_404(Person, pk=person_id)
    context = {'person': person}
    return render(request, 'authapp/person.html', context)